<?php
$admin_password = "securepassword"; // Ändere das Passwort zu einem sicheren Wert

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $headers = apache_request_headers();
    $content = json_decode(file_get_contents("php://input"), true);

    if (!isset($headers['Authorization']) || $headers['Authorization'] !== "Bearer $admin_password") {
        http_response_code(403);
        echo json_encode(["error" => "Unauthorized"]);
        exit;
    }

    $title = $content["title"];
    $htmlContent = $content["content"];
    $version = $content["version"] ?? "1.0";
    $release_date = $content["release_date"] ?? date("Y-m-d");
    $changelog = $content["changelog"] ?? "No changelog available.";
    $description = $content["description"] ?? "No description provided.";
    $download_link = $content["download_link"] ?? "#";

    $filename = "pages/" . preg_replace("/[^a-zA-Z0-9]/", "-", strtolower($title)) . ".html";

    // Lade Template und ersetze Platzhalter
    $template = file_get_contents("pages/template.html");
    $newPage = str_replace(
        ["{{title}}", "{{version}}", "{{release_date}}", "{{changelog}}", "{{description}}", "{{download_link}}"],
        [$title, $version, $release_date, $changelog, $description, $download_link],
        $template
    );

    // Speichern als HTML-Datei
    file_put_contents($filename, $newPage);

    http_response_code(200);
    echo json_encode(["success" => true, "filename" => $filename]);
}
?>
