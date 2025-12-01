<?php

function parseMarkdown($text) {
    if (empty($text)) return '';
    
    $text = htmlspecialchars($text);
    
    $codeBlocks = [];
    $codeIndex = 0;
    
    $text = preg_replace_callback(
        '/```([\s\S]*?)```/',
        function($matches) use (&$codeBlocks, &$codeIndex) {
            $code = $matches[1];
            $code = trim($code, "\n\r");
            $placeholder = "___CODEBLOCK_" . $codeIndex . "___";
            $codeBlocks[$placeholder] = '<pre class="code-block bg-light p-3 rounded mt-2 mb-2"><code>' . $code . '</code></pre>';
            $codeIndex++;
            return $placeholder;
        },
        $text
    );
    
    $lines = explode("\n", $text);
    $paragraphs = [];
    foreach ($lines as $line) {
        $line = trim($line);
        if (!empty($line)) {
            $paragraphs[] = '<p>' . $line . '</p>';
        }
    }
    $text = implode('', $paragraphs);
    
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    foreach ($codeBlocks as $placeholder => $codeBlock) {
        $text = str_replace($placeholder, $codeBlock, $text);
    }
    
    return $text;
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) {
        return 'just now';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minute' . ($minutes > 1 ? 's' : '') . ' ago';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y g:i A', strtotime($datetime));
    }
}

function createPreview($text, $maxLength = 200) {
    $text = htmlspecialchars($text);
    
    $text = preg_replace('/```[\s\S]*?```/', '[code]', $text);
    
    $text = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $text);
    
    $text = preg_replace('/\s+/', ' ', $text);
    $text = trim($text);
    
    if (strlen($text) > $maxLength) {
        $text = substr($text, 0, $maxLength) . '...';
    }
    
    return $text;
}

function getUserVotes($pdo, $userId, $questionIds = [], $answerIds = []) {
    $votes = ['questions' => [], 'answers' => []];
    
    if (!$userId) {
        return $votes;
    }
    
    if (!empty($questionIds)) {
        $placeholders = str_repeat('?,', count($questionIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT question_id, vote_type FROM votes WHERE user_id = ? AND question_id IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $questionIds));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $votes['questions'][$row['question_id']] = $row['vote_type'];
        }
    }
    
    if (!empty($answerIds)) {
        $placeholders = str_repeat('?,', count($answerIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT answer_id, vote_type FROM votes WHERE user_id = ? AND answer_id IN ($placeholders)");
        $stmt->execute(array_merge([$userId], $answerIds));
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $votes['answers'][$row['answer_id']] = $row['vote_type'];
        }
    }
    
    return $votes;
}

function getUserVoteForItem($pdo, $userId, $targetType, $targetId) {
    if (!$userId || !in_array($targetType, ['question', 'answer'])) {
        return null;
    }
    
    if ($targetType === 'question') {
        $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND question_id = ?");
    } else {
        $stmt = $pdo->prepare("SELECT vote_type FROM votes WHERE user_id = ? AND answer_id = ?");
    }
    
    $stmt->execute([$userId, $targetId]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result ? $result['vote_type'] : null;
}
?>