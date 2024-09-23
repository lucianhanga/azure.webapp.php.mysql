<?php

function getSecretFromKeyVault($accessToken, $vaultName, $secretName) {
    // Construct the URL for the specific secret in the Key Vault
    $url = "https://$vaultName.vault.azure.net/secrets/$secretName?api-version=7.3";
    echo "<p><strong>Step 1:</strong> Constructed the URL for the Key Vault request: <code>$url</code></p>";

    // Initialize cURL session
    echo "<p><strong>Step 2:</strong> Initializing cURL session...</p>";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer $accessToken",
        "Content-Type: application/json"
    ]);
    echo "<p><strong>Step 3:</strong> Set up cURL options, including authorization header and content type.</p>";

    // Execute the request
    echo "<p><strong>Step 4:</strong> Sending request to Azure Key Vault...</p>";
    $response = curl_exec($ch);

    // Check for connection errors
    if (curl_errno($ch)) {
        $errorMsg = curl_error($ch);
        echo "<p><strong>Error:</strong> Failed to connect to Azure Key Vault. cURL Error: $errorMsg</p>";
        curl_close($ch);
        return null;
    } else {
        echo "<p><strong>Step 5:</strong> Request sent successfully. Parsing response...</p>";
    }

    // Parse the response
    $responseArray = json_decode($response, true);

    // Check if the response contains the secret value
    if (isset($responseArray['value'])) {
        echo "<p><strong>Step 6:</strong> Secret successfully retrieved from Key Vault.</p>";
        curl_close($ch);
        return $responseArray['value']; // Return the actual secret value
    } else {
        echo "<p><strong>Error:</strong> Secret not found in the response.</p>";
        echo "<p>Response: <pre>" . print_r($responseArray, true) . "</pre></p>";
        curl_close($ch);
        return null;
    }
}

function getPersonData($host, $dbname, $mysql_username, $mysql_password, $certificate) {
    // Start HTML output directly in the function
    echo "<h2>MySQL Database Connection and Data Retrieval Process</h2>";
    echo "<p>Starting the process to connect to the MySQL database and retrieve data...</p>";

    // Step 1: Construct the Data Source Name (DSN)
    $dsn = "mysql:host=$host;dbname=$dbname";
    echo "<p><strong>Step 1:</strong> Constructed the DSN for the database connection:</p>";
    echo "<code>$dsn</code><br><br>";

    // Step 2: Define the connection options
    echo "<p><strong>Step 2:</strong> Setting up connection options for PDO:</p>";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_SSL_CA => $certificate,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false
    ];
    echo "<ul>";
    foreach ($options as $option => $value) {
        echo "<li><strong>$option:</strong> " . (is_bool($value) ? var_export($value, true) : $value) . "</li>";
    }
    echo "</ul>";

    try {
        // Step 3: Create a new PDO instance
        echo "<p><strong>Step 3:</strong> Attempting to connect to the database...</p>";
        $pdo = new PDO($dsn, $mysql_username, $mysql_password, $options);
        echo "<p style='color: green;'><strong>Step 4:</strong> Connection to the database successful!</p>";

        // Step 4: Query to fetch data from the person table
        echo "<p><strong>Step 5:</strong> Executing query to retrieve data from the 'person' table...</p>";
        $query = $pdo->query("SELECT firstname, lastname, sex, age FROM person");

        // Step 5: Fetch all records as an associative array
        echo "<p><strong>Step 6:</strong> Fetching all records from the query result...</p>";
        $persons = $query->fetchAll();

        // Check if data was retrieved
        if ($persons) {
            echo "<p style='color: green;'><strong>Step 7:</strong> Data retrieval successful. Found " . count($persons) . " records.</p>";
        } else {
            echo "<p style='color: orange;'><strong>Step 7:</strong> No records found in the 'person' table.</p>";
        }

        // Return only the data from the database
        return $persons;

    } catch (PDOException $e) {
        // Step 8: If connection fails, display the error message directly
        $errorMsg = $e->getMessage();
        echo "<p style='color: red;'><strong>Error:</strong> Connection failed: " . htmlspecialchars($errorMsg) . "</p>";
        return null;
    }
}

// Database configuration
$host = 'mysql-webappphpmysql.mysql.database.azure.com';
$dbname = 'db-webappphpmysql';
$certificate = 'DigiCertGlobalRootCA.crt.pem';
$keyvalut_name = 'kv-webappphpmysql';
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsIng1dCI6Ikg5bmo1QU9Tc3dNcGhnMVNGeDdqYVYtbEI5dyIsImtpZCI6Ikg5bmo1QU9Tc3dNcGhnMVNGeDdqYVYtbEI5dyJ9.eyJhdWQiOiJjZmE4YjMzOS04MmEyLTQ3MWEtYTNjOS0wZmMwYmU3YTQwOTMiLCJpc3MiOiJodHRwczovL3N0cy53aW5kb3dzLm5ldC85ZGRmZjYxZC0xZTBmLTQyNWEtOTY0My1kOGE3Y2Q5YWQ0MDkvIiwiaWF0IjoxNzI2OTk2NDMyLCJuYmYiOjE3MjY5OTY0MzIsImV4cCI6MTcyNzAwMTE3OSwiYWNyIjoiMSIsImFpbyI6IkFZUUFlLzhYQUFBQWRiMDdjSG9PYkhKYUJwK1p3Z1JCQlovcEVTaGlFV01CTzg1SldCU3pXUENKNFVrRE5qamthcTJHdWVOUlVIK0FjTU1lVjM5clliWnI1Q0RTaXpwYThwczIyTk43RU0xWWl0MzZBcTVxZFlDemF5TlJIcmkxMSt6eW93bmVGYVF2TTBmUjl4SUs5Q01maytUSGRiTWo1WHJVeVFPamVYSEhTSDVwYzRWcTdEcz0iLCJhbHRzZWNpZCI6IjE6bGl2ZS5jb206MDAwMTRCNTBCMkY1NzAxNCIsImFtciI6WyJwd2QiLCJtZmEiXSwiYXBwaWQiOiJiNjc3YzI5MC1jZjRiLTRhOGUtYTYwZS05MWJhNjUwYTRhYmUiLCJhcHBpZGFjciI6IjAiLCJlbWFpbCI6Imx1Y2lhbmhhbmdhQGhvdG1haWwuY29tIiwiZmFtaWx5X25hbWUiOiJIYW5nYSIsImdpdmVuX25hbWUiOiJMdWNpYW4iLCJncm91cHMiOlsiNDBmNjRmN2EtNDBmZC00YzY2LTk4MjctYmU0YWUzOTZkNzYxIl0sImlkcCI6ImxpdmUuY29tIiwiaWR0eXAiOiJ1c2VyIiwiaXBhZGRyIjoiNDYuMjQ0LjI0Ni41MSIsIm5hbWUiOiJMdWNpYW4gSGFuZ2EiLCJvaWQiOiJiZGI1ZjEzMy0wM2ZmLTQxZTktYTNlNi02NzA2MWJkZGJjOWEiLCJwdWlkIjoiMTAwMzIwMDIwRDMyNDI4MyIsInJoIjoiMC5BWGtBSGZiZm5ROGVXa0tXUTlpbnpaclVDVG16cU0taWdocEhvOGtQd0w1NlFKT1VBSVkuIiwic2NwIjoidXNlcl9pbXBlcnNvbmF0aW9uIiwic3ViIjoiXzJ4WlF1RmlEb0Y2dURuamVWdmpiQ0FSQ1FHT0p5eS01MlJfU0Qza19UayIsInRpZCI6IjlkZGZmNjFkLTFlMGYtNDI1YS05NjQzLWQ4YTdjZDlhZDQwOSIsInVuaXF1ZV9uYW1lIjoibGl2ZS5jb20jbHVjaWFuaGFuZ2FAaG90bWFpbC5jb20iLCJ1dGkiOiJoN2toVGV3cW4wS2VJMWFkV1BGTkFBIiwidmVyIjoiMS4wIiwid2lkcyI6WyI2MmU5MDM5NC02OWY1LTQyMzctOTE5MC0wMTIxNzcxNDVlMTAiLCJiNzlmYmY0ZC0zZWY5LTQ2ODktODE0My03NmIxOTRlODU1MDkiXSwieG1zX2lkcmVsIjoiMSAxMiJ9.YzC-vFrWJrg5ZTcok2W2ZHxGyPixFyGvhh_RLYPJrHuQqWZsEl-sjIybuDb2yY08r2eAIpc-vIKYrCtWzxwoE6AyggPOEQrFRrHkn5jGh3nnieuAaJQdbtTOZtcuDxZaBT7Qqj17EPIN4tGWil1QvJduMhGp9mmKWuJwbzfnCDnOvBCWNZ4GMSe0Bp-I7IdwFT3hjAiZ1W_Zcs2LZMmCSnWTVrOD6aZeoX8bWevr-eeWvslk2qDX6zhk3IX9RaUjRotMsIMSSSWPbuYDqeVmAIG9oS7xoOYt_fvEJXeNDUhdw1hUXsgeg_4JW8e0hbBGvMZSSjVkWzNBPmgDMu6j5Q";

// get the username and password from Key Vault
echo "<h1> Get the username from Key Vault </h1>";
$mysql_username = getSecretFromKeyVault($token, $keyvalut_name, "mysql-username");
if ($mysql_username) {
    echo "<p><strong>Secret Value:</strong> <span style='color: red;'>****</span> (masked for security)</p>";
} else {
    echo "<p><strong>Error:</strong> Failed to retrieve secret value.</p>";
}
echo "<h1> Get the password from Key Vault </h1>";
$mysql_password = getSecretFromKeyVault($token, $keyvalut_name, "mysql-password");
if ($mysql_password) {
    echo "<p><strong>Secret Value:</strong> <span style='color: red;'>****</span> (masked for security)</p>";
} else {
    echo "<p><strong>Error:</strong> Failed to retrieve secret value.</p>";
}


// get the data from the database
echo "<h1> Get the data from the database </h1>";
$persons = getPersonData($host, $dbname, $mysql_username, $mysql_password, $certificate);
// Render the web page
echo '<html><head><title>Person Records</title></head><body>';
echo '<h1>Person Records</h1>';

if (is_array($persons)) {
    if (!empty($persons)) {
        echo '<h2 style="color: green;">Connection successful! Here are the records:</h2>';
        echo '<table border="1" cellpadding="10" cellspacing="0">';
        echo '<tr><th>First Name</th><th>Last Name</th><th>Sex</th><th>Age</th></tr>';
        foreach ($persons as $person) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($person['firstname']) . '</td>';
            echo '<td>' . htmlspecialchars($person['lastname']) . '</td>';
            echo '<td>' . htmlspecialchars($person['sex']) . '</td>';
            echo '<td>' . htmlspecialchars($person['age']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<h2 style="color: orange;">No records found in the person table.</h2>';
    }
} else {
    // If the data is not an array, it's an error message
    echo '<h2 style="color: red;">' . htmlspecialchars($persons) . '</h2>';
}

echo '</body></html>';

?>
