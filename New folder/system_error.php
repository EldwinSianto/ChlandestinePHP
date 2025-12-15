<?php
/**
 * SYSTEM ERROR LOGGER
 * Digunakan untuk mencatat error sistem KRITIS ke tabel system_errors
 */

function logSystemError(
    mysqli $conn,
    ?int $user_id,
    string $error_code,
    string $error_message,
    array $context = []
) {
    $context_json = empty($context)
        ? null
        : json_encode($context, JSON_UNESCAPED_UNICODE);

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO system_errors 
         (user_id, error_code, error_message, context)
         VALUES (?, ?, ?, ?)"
    );

    // Fallback jika DB error (last resort)
    if (!$stmt) {
        error_log(
            "[SYSTEM_ERROR_LOGGER_FAILED] " .
            mysqli_error($conn) .
            " | code=$error_code"
        );
        return;
    }

    mysqli_stmt_bind_param(
        $stmt,
        "isss",
        $user_id,
        $error_code,
        $error_message,
        $context_json
    );

    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
