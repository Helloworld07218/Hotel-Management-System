<?php
// Start a session to remember our progress
session_start();

// --- Define file and directory names ---
$dirName = 'hotel_data';
$renamedDirName = 'hotel_archive';
$baseFileName = 'guest_list.txt';
$baseRenamedFileName = 'checked_in_guests.txt';

$output = "";

// Check if the form has been submitted
if (isset($_POST['submit'])) {
    
    $mode = $_POST['file_mode'];
    
    switch ($mode) {
        
        // TASK 1: Create Directory and Open/Create File
        case 'task1':
            $output .= "<h3>Task 1: Create Directory and File</h3>";
            if (!is_dir($dirName)) {
                if (mkdir($dirName)) {
                    $output .= "<p class'success'>Directory '<b>$dirName</b>' created.</p>";
                } else {
                    $output .= "<p class='error'>Error: Failed to create directory '<b>$dirName</b>'.</p>";
                }
            } else {
                $output .= "<p class='info'>Info: Directory '<b>$dirName</b>' already exists.</p>";
            }
            
            $filePath = $dirName . '/' . $baseFileName;
            $fileHandle = @fopen($filePath, 'w'); // 'w' creates the file
            if ($fileHandle) {
                fclose($fileHandle);
                $output .= "<p class='success'>File '<b>$filePath</b>' created and closed.</p>";
                // Save progress in session
                $_SESSION['dir_path'] = $dirName;
                $_SESSION['file_path'] = $filePath;
                // Clear any leftover rename data
                unset($_SESSION['renamed_file_path']); 
            } else {
                $output .= "<p class='error'>Error: Could not create file '<b>$filePath</b>'.</p>";
            }
            break;
            
        // TASK 2 & 3: Write to and Read from File (using feof)
        case 'task2':
            $output .= "<h3>Task 2 & 3: Write and Read File</h3>";
            if (!isset($_SESSION['file_path'])) {
                $output .= "<p class='error'>Error: Please run Task 1 first.</p>";
                break;
            }
            
            $filePath = $_SESSION['file_path'];
            $guests = [
                "Ooi Kai Xin - 2025-11-10\n", "Gan Ting Yu - 2025-11-11\n",
                "Yeap Hong Lin - 2025-11-12\n", "Kelson - 2025-11-13\n",
                "Eve Davis - 2025-11-14\n"
            ];
            
            // Write to file
            if ($fileHandle = @fopen($filePath, 'w')) {
                foreach ($guests as $guest) {
                    fwrite($fileHandle, $guest);
                }
                fclose($fileHandle);
                $output .= "<p class='success'>Successfully wrote 5 guests to '<b>$filePath</b>'.</p>";
            } else {
                $output .= "<p class='error'>Error: Cannot open file for writing.</p>";
            }
            
            // Read from file
            if ($fileHandle = @fopen($filePath, 'r')) {
                $output .= "<p>Contents of '<b>$filePath</b>':</p>";
                $output .= "<pre class='file-content'>";
                while (!feof($fileHandle)) { // Using feof() as required
                    $output .= htmlspecialchars(fgets($fileHandle));
                }
                $output .= "</pre>";
                fclose($fileHandle);
            } else {
                $output .= "<p class='error'>Error: Cannot open file for reading.</p>";
            }
            break;

        // TASK 4: Read Character by Character (Count)
        case 'task3':
            $output .= "<h3>Task 4: Count Characters</h3>";
            if (!isset($_SESSION['file_path'])) {
                $output .= "<p class='error'>Error: Please run Task 1 & 2 first.</p>";
                break;
            }
            
            $filePath = $_SESSION['file_path'];
            if ($fileHandle = @fopen($filePath, 'r')) {
                $charCount = 0;
                while (fgetc($fileHandle) !== false) {
                    $charCount++;
                }
                fclose($fileHandle);
                $output .= "<p class='success'>Total character count: <b>$charCount</b></p>";
            } else {
                $output .= "<p class='error'>Error: Cannot open file for counting.</p>";
            }
            break;

        // TASK 5a: Rename File
        case 'task4_rename':
            $output .= "<h3>Task 5a: Rename File</h3>";
            if (!isset($_SESSION['file_path'])) {
                $output .= "<p class'error'>Error: File '<b>$baseFileName</b>' does not exist. Please run Task 1 first.</p>";
                break;
            }
            
            $filePath = $_SESSION['file_path'];
            $renamedPath = $_SESSION['dir_path'] . '/' . $baseRenamedFileName;
            
            if (file_exists($filePath) && rename($filePath, $renamedPath)) {
                $output .= "<p class='success'>File '<b>$filePath</b>' renamed to '<b>$renamedPath</b>'.</p>";
                // Update session: remove old path, add new one
                unset($_SESSION['file_path']);
                $_SESSION['renamed_file_path'] = $renamedPath;
            } else {
                $output .= "<p class='error'>Error: Failed to rename file.</p>";
            }
            break;

        // TASK 5b: Delete File
        case 'task5_delete':
            $output .= "<h3>Task 5b: Delete File</h3>";
            if (!isset($_SESSION['renamed_file_path'])) {
                $output .= "<p class'error'>Error: File '<b>$baseRenamedFileName</b>' does not exist. Please run 'Rename File' first.</p>";
                break;
            }
            
            $deletePath = $_SESSION['renamed_file_path'];
            
            if (unlink($deletePath)) {
                $output .= "<p class='success'>File '<b>$deletePath</b>' successfully deleted.</p>";
                unset($_SESSION['renamed_file_path']); // Clear from session
            } else {
                $output .= "<p class='error'>Error: Failed to delete file '<b>$deletePath</b>'.</p>";
            }
            break;

        // TASK 6: Directory Operations
        case 'task6_dir':
            $output .= "<h3>Task 6: Directory Operations</h3>";
            if (!isset($_SESSION['dir_path'])) {
                $output .= "<p class='error'>Error: Please run Task 1 first.</p>";
                break;
            }
            
            $dirPath = $_SESSION['dir_path'];
            
            // List files
            $output .= "<p>Files in '<b>$dirPath</b>' (should be empty):</p><ul>";
            $files = scandir($dirPath);
            $isEmpty = true;
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    $output .= "<li>$file</li>";
                    $isEmpty = false;
                }
            }
            if ($isEmpty) {
                $output .= "<li>(Directory is empty)</li>";
            }
            $output .= "</ul>";
            
            if ($isEmpty) {
                if (rename($dirPath, $renamedDirName)) {
                    $output .= "<p class'success'>Directory '<b>$dirPath</b>' renamed to '<b>$renamedDirName</b>'.</p>";
                    if (rmdir($renamedDirName)) {
                        $output .= "<p class='success'>Directory '<b>$renamedDirName</b>' deleted.</p>";
                        unset($_SESSION['dir_path']); // Remove from session
                    } else {
                        $output .= "<p class='error'>Error: Failed to delete directory.</p>";
                    }
                } else {
                    $output .= "<p class='error'>Error: Failed to rename directory.</p>";
                }
            } else {
                $output .= "<p class='error'>Error: Directory not empty. Cannot rename/delete.</p>";
            }
            break;
            
        // Reset/Clear Session
        case 'reset':
            session_unset();
            session_destroy();
            $output = "<p class='success'>All tasks and files have been reset.</p>";
            // Clean up leftover files/dirs
            @unlink($dirName . '/' . $baseFileName);
            @unlink($dirName . '/' . $baseRenamedFileName);
            @rmdir($dirName);
            @rmdir($renamedDirName);
            break;
            
        default:
            $output = "<p class='info'>Please select a valid task from the list.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            padding-top: 20px;
        }
        h1 {
            color: #333;
            margin: 0;
        }
        hr.dotted {
            border: none;
            border-top: 2px dotted #555;
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
        }
        .form-box {
            border: 2px solid #000;
            padding: 20px 40px;
            margin-top: 20px;
            background: #fff;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .form-box h3 {
            text-decoration: underline;
            font-weight: bold;
            color: #222;
        }
        .form-box label {
            display: block;
            text-decoration: underline;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 5px;
        }
        .form-box select {
            padding: 8px;
            font-size: 1em;
            width: 250px; /* Widened for new labels */
        }
        .form-box input[type="submit"] {
            padding: 8px 16px;
            margin-top: 20px;
            font-size: 1em;
            cursor: pointer;
        }
        .output-box {
            width: 100%;
            max-width: 560px; /* Aligns with the form box width */
            margin-top: 20px;
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            min-height: 50px;
            line-height: 1.6;
        }
        .file-content {
            background: #333;
            color: #fff;
            padding: 10px;
            border-radius: 5px;
            white-space: pre-wrap;
        }
        .success { color: #28a745; font-weight: bold; }
        .error { color: #dc3545; font-weight: bold; }
        .info { color: #17a2b8; }
    </style>
</head>
<body>

    <h1>HOTEL MANAGEMENT SYSTEM</h1>
    <hr class="dotted">

    <div class="form-box">
        <form method="POST" action="hotel_manager.php" onsubmit="return checkTask()">
            <h3>Dear Receptionist</h3>
            
            <label for="file_mode">Select File Mode</label>
            <select name="file_mode" id="file_mode">
                <option value="">--Select Mode--</option>
                <option value="task1">1. Create Dir &amp; Open File (Task 1)</option>
                <option value="task2">2. Write &amp; Read File (Tasks 2,3)</option>
                <option value="task3">3. Count Characters (Task 4)</option>
                <option value="task4_rename">4. Rename File (Task 5a)</option>
                <option value="task5_delete">5. Delete File (Task 5b)</option>
                <option value="task6_dir">6. Directory Operations (Task 6)</option>
                <option value="" disabled>-----------------</option>
                <option value="reset">!! RESET ALL !!</option>
            </select>
            
            <br>
            <input type="submit" name="submit" value="Submit">
        </form>
    </div>

    <div class="output-box">
        <?php echo $output; ?>
    </div>

    <script>
    function checkTask() {
        var selectedTask = document.getElementById('file_mode').value;
        
        // Check for destructive actions
        if (selectedTask === 'task5_delete' || selectedTask === 'task6_dir' || selectedTask === 'reset') {
            // Display a JavaScript confirm box
            return confirm('Are you sure you want to perform this action? This operation cannot be undone.');
        }
        
        // For all other tasks (including rename), submit the form without confirmation
        return true;
    }
    </script>
</body>
</html>