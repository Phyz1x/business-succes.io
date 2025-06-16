<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Odredi email adresu primaoca
    $receiving_email = "tvoja_email_adresa@example.com"; // ZAMENI OVDE SVOJU EMAIL ADRESU!

    // Pripremi podatke iz forme
    $formData = [];
    foreach ($_POST as $key => $value) {
        // Sanitize input to prevent injection
        $sanitized_value = htmlspecialchars(strip_tags(trim($value)));
        $formData[$key] = $sanitized_value;
    }

    // Formiraj subject za email
    $subject = "Novi test rezultat: Verhandlungstyp";

    // Formiraj telo emaila
    $email_body = "Novi korisnik je popunio Verhandlungstyp test.\n\n";
    $email_body .= "--- Kontakt podaci ---\n";
    $email_body .= "Vorname: " . ($formData['vorname'] ?? 'N/A') . "\n";
    $email_body .= "Nachname: " . ($formData['nachname'] ?? 'N/A') . "\n";
    $email_body .= "E-Mail: " . ($formData['email'] ?? 'N/A') . "\n";
    $email_body .= "Unternehmen: " . ($formData['unternehmen'] ?? 'N/A') . "\n";
    $email_body .= "Position: " . ($formData['position'] ?? 'N/A') . "\n";
    $email_body .= "Datenschutz zugestimmt: " . (isset($formData['datenschutz']) ? 'Ja' : 'Nein') . "\n\n";

    $email_body .= "--- Odgovori na pitanja ---\n";
    for ($i = 1; $i <= 15; $i++) {
        $question_key = "frage" . $i;
        if (isset($formData[$question_key])) {
            $answer = $formData[$question_key];
            
            // Handle checkbox answers (if multiple are selected)
            if (is_array($answer)) {
                $email_body .= "Frage " . $i . ": " . implode(", ", $answer) . "\n";
            } else {
                $email_body .= "Frage " . $i . ": " . $answer . "\n";
            }
        }
    }
    $email_body .= "\n--------------------------\n";
    $email_body .= "Ovo je automatski generisana poruka. Molimo Vas da ne odgovarate direktno na nju.";


    // Postavi headere
    $headers = "From: webmaster@tvoj-domen.com\r\n"; // Zameni sa validnom email adresom domena!
    $headers .= "Reply-To: " . ($formData['email'] ?? $receiving_email) . "\r\n";
    $headers .= "Content-type: text/plain; charset=UTF-8\r\n";

    // Pokušaj da pošalješ email
    if (mail($receiving_email, $subject, $email_body, $headers)) {
        // Uspešno poslato - preusmeri na thank you stranicu (ili nazad na index.html)
        // Možeš preusmeriti nazad na index.html sa hash-om ili parametrom
        // ili jednostavno ispisati JSON uspeh za AJAX
        echo json_encode(["status" => "success", "message" => "Email successfully sent!"]);
    } else {
        // Greška pri slanju
        echo json_encode(["status" => "error", "message" => "Failed to send email."]);
    }
} else {
    // Nije POST zahtev
    http_response_code(405); // Method Not Allowed
    echo json_encode(["status" => "error", "message" => "Invalid request method."]);
}
?> 