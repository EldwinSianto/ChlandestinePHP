<?php
/**
 * ============================================================
 * SYSTEM ERROR LOGGER
 * ============================================================
 * Digunakan untuk mencatat:
 * - SYSTEM ERROR (DB, runtime, config)
 * - LOGIC ERROR (business rule violation)
 * - AUTHORIZATION ERROR
 *
 * Target table: system_errors
 * ============================================================
 */

function logSystemError(
    mysqli $conn,
    ?int $user_id,
    string $error_code,
    string $error_message,
    array $context = []
): void {

    // Encode context ke JSON (jika ada)
    $context_json = empty($context)
        ? null
        : json_encode($context, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);

    // Prepare statement
    $stmt = $conn->prepare(
        "INSERT INTO system_errors
         (user_id, error_code, error_message, context)
         VALUES (?, ?, ?, ?)"
    );

    // Fallback jika DB logging gagal (LAST RESORT)
    if (!$stmt) {
        error_log(
            "[SYSTEM_ERROR_LOGGER_FAILED] " .
            $conn->error .
            " | code={$error_code} | msg={$error_message}"
        );
        return;
    }

    // Bind parameter
    $stmt->bind_param(
        "isss",
        $user_id,
        $error_code,
        $error_message,
        $context_json
    );

    // Execute (jangan crash walau gagal)
    if (!$stmt->execute()) {
        error_log(
            "[SYSTEM_ERROR_INSERT_FAILED] " .
            $stmt->error .
            " | code={$error_code}"
        );
    }

    $stmt->close();
}
