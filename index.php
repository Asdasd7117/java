<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['zip_file'])) {
    $uploadDir = 'uploads/';
    $zipFile = $uploadDir . basename($_FILES['zip_file']['name']);

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    if (move_uploaded_file($_FILES['zip_file']['tmp_name'], $zipFile)) {
        $extractDir = $uploadDir . pathinfo($zipFile, PATHINFO_FILENAME);
        mkdir($extractDir, 0777, true);

        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractDir);
            $zip->close();

            $gradleCmd = "cd $extractDir && ./gradlew assembleDebug";
            shell_exec($gradleCmd);

            $apkPath = glob("$extractDir/app/build/outputs/apk/debug/*.apk")[0] ?? '';
            if ($apkPath && file_exists($apkPath)) {
                echo json_encode(["success" => true, "apk" => $apkPath]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to build APK"]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "Failed to extract ZIP"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "File upload failed"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request"]);
}
?>
