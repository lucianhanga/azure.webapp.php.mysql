<?php

// get the access token from Azure AD takiing in account 
// that the WebApp is a system managed identity
function getAccessToken() {
    // get from the environment variable the IDENTIY_ENDPOINT
    $identity_endpoint = getenv('IDENTITY_ENDPOINT');
    echo "<p><strong>Step 0.1:</strong> Retrieved the identity header from environment variables: <code>$identity_endpoint</code></p>"; 

    // get from the environment variable the IDENTIY_HEADER
    $identity_header = getenv('IDENTITY_HEADER');
    echo "<p><strong>Step 0.2:</strong> Retrieved the identity header from environment variables: <code>$identity_header</code></p>";

    // the intended resource to access
    $resource = "https://vault.azure.net";
    echo "<p><strong>Step 0.3:</strong> Retrieved the resource from environment variables: <code>$resource</code></p>";


    // if user-managed identity
    // $client_id = "cf8b339-82a2-471a-a3c9-0fc0be7a4093";
    // echo "<p><strong>Step 0.4:</strong> Retrieved the client_id from environment variables: <code>$client_id</code></p>";

    // if system-managed identity
    // there is no client_id (of the user managed identity) required
    $client_id = "";
    echo "<p><strong>Step 0.4:</strong>No ClientID</code></p>";

    // the api version
    $api_version = "2019-08-01";
    echo "<p><strong>Step 0.5:</strong> Retrieved the api_version from environment variables: <code>$api_version</code></p>";

    // build the curl command
    $curl = curl_init();

    // setup also the parameters for the curl command 
    $base_url = $identity_endpoint;
    $params = array(
        'api-version' => $api_version,
        'resource' => $resource
    );

    // Add client_id to params only if it is defined and not empty
    if (!empty($client_id)) {
        $params['client_id'] = $client_id;
    } else {
        echo "<p><strong>Step 0.5:</strong> No client_id provided for system-assigned managed identity.</p>";
    }
    // Build the query string using http_build_query
    $query_string = http_build_query($params);
    // Combine base URL with query string
    $full_url = $base_url . '?' . $query_string;

    echo "<p><strong>Step 0.6:</strong> Constructed URL for cURL request: <code>$full_url</code></p>";
    
    curl_setopt($curl, CURLOPT_URL, $full_url);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-IDENTITY-HEADER: $identity_header"));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    # is GET
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
    $result = curl_exec($curl);

    // check for any errors
    if (curl_errno($curl)) {
        echo "<p><strong>Error:</strong> cURL request failed. Error: " . curl_error($curl) . "</p>";
        curl_close($curl);
        return null;
    }

    // decode the result
    $response = json_decode($result, true);
    // print the response
    $response_print = $response;
    # mask the token for security reasons
    $response_print['access_token'] = "****";
    echo "<p><strong>Step 0.6:</strong> Retrieved the response from the identity endpoint: <pre>" . print_r($response_print, true) . "</pre></p>";
    // close the curl
    curl_close($curl);
    // return the access token
    return $response['access_token'];
}

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
$host = 'mysql-webappphpmysql7.mysql.database.azure.com';
$dbname = 'db-webappphpmysql';
$certificate = 'DigiCertGlobalRootCA.crt.pem';
$keyvalut_name = 'kv-webappphpmysql7';

// get the access token from Azure AD
echo "<h1> Get the access token from Azure AD </h1>";
$token = getAccessToken();
if ($token) {
    echo "<p><strong>Token:</strong> <span style='color: red;'>****</span> (masked for security)</p>";
} else {
    echo "<p><strong>Error:</strong> Failed to retrieve access token.</p>";
    // finish the script
    exit();
}
// get the username and password from Key Vault
echo "<h1> Get the username from Key Vault </h1>";
$mysql_username = getSecretFromKeyVault($token, $keyvalut_name, "mysql-username");
if ($mysql_username) {
    echo "<p><strong>Secret Value:</strong> <span style='color: red;'>****</span> (masked for security)</p>";
} else {
    echo "<p><strong>Error:</strong> Failed to retrieve secret value.</p>";
    // exit here
    exit();
}
echo "<h1> Get the password from Key Vault </h1>";
$mysql_password = getSecretFromKeyVault($token, $keyvalut_name, "mysql-password");
if ($mysql_password) {
    echo "<p><strong>Secret Value:</strong> <span style='color: red;'>****</span> (masked for security)</p>";
} else {
    echo "<p><strong>Error:</strong> Failed to retrieve secret value.</p>";
    // exit here
    exit();
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
