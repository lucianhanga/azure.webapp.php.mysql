hi Ben,  
and thank you for your answer.

I understand your point regarding the Managed User Identities. However, it should work also with Managed System Identities, right ?

Here is more about the resources:

print the app service relevant information

```bash
az webapp show \
    --resource-group  rg-webappphpmysql\
    --name  webapp-webappphpmysql3\
    --query "{name: name, objectId: identity.principalId, systemIdentityEnabled: identity.type == 'SystemAssigned' || identity.type == 'SystemAssigned, UserAssigned'}"
```
output:

```json
{
  "name": "webapp-webappphpmysql3",
  "objectId": "a081e3e8-9ec1-4c6a-bcc2-4074acec0fa4",
  "systemIdentityEnabled": true
}
```

print the key valut policy information related to the app service

```bash
az keyvault show \
    --name  kv-webappphpmysql3 \
    --resource-group rg-webappphpmysql \
    --query "properties.accessPolicies[?objectId=='a081e3e8-9ec1-4c6a-bcc2-4074acec0fa4']"
```
output:

```json
[
  {
    "applicationId": null,
    "objectId": "a081e3e8-9ec1-4c6a-bcc2-4074acec0fa4",
    "permissions": {
      "certificates": [],
      "keys": [],
      "secrets": [
        "Get"
      ],
      "storage": []
    },
    "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409"
  }
]
```

so I should have the permission to get the secret from the key vault from the app service.


the deployed php application is:

```php
<?php
// MSI endpoint for access token
$endpoint = "http://169.254.169.254/metadata/identity/oauth2/token";

// Resource for the access token. This example uses Azure Resource Manager (ARM) API.
// Change this to the appropriate resource for your needs (e.g., Graph API, Key Vault, etc.)
$resource = "https://valut.azure.com/";

// Setting up the cURL request to get the access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $endpoint . "?api-version=2018-02-01&resource=" . urlencode($resource));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Metadata: true' // Required header for MSI endpoint
));

// Execute the cURL request and get the response
$response = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
    exit;
}

// Close the cURL session
curl_close($ch);

// Decode the JSON response
$tokenData = json_decode($response, true);

// Check if the access token was retrieved successfully
if (isset($tokenData['access_token'])) {
    $accessToken = $tokenData['access_token'];
    echo "Access Token: " . $accessToken;
} else {
    echo "Failed to retrieve access token. Response: " . $response;
}
?>
``` 
and the output in the browser is:

```yaml
Error:Failed to connect to 169.254.169.254 port 80: Connection refused
```

same error from the app service console 

```bash
   __ __          __      __    _ __     
   / //_/_  _ ____/ /_  __/ /   (_) /____ 
  / ,< / / / / __  / / / / /   / / __/ _  
 / /| / /_/ / /_/ / /_/ / /___/ / /_/  __/
/_/ |_\__,_/\__,_/\__,_/_____/_/\__/\___/ 

                                          
DEBUG CONSOLE | AZURE APP SERVICE ON LINUX

Documentation: http://aka.ms/webapp-linux
Kudu Version : 20240822.2
Commit       : a86ad9d31002b0e2111a20f21ebaeae4be986b94

kudu_ssh_user@webapp-web_kudu_b4159fe590:/$ curl -H "Metadata: true" "http://169.254.169.254/metadata/identity/oauth2/token?api-version=2018-02-01&resource=https://valut.azure.com/"
curl: (7) Failed to connect to 169.254.169.254 port 80: Connection refused
kudu_ssh_user@webapp-web_kudu_b4159fe590:/$ 
```

I list here also all the information about the app service. 
The `connection refused` error I think is not related to the *Key Vault* at all, but rather with some app settings. I didn't do anything special in the app, just the standard attributes.

```json
{
  "appServicePlanId": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webappphpmysql/providers/Microsoft.Web/serverfarms/asp-webappphpmysql3",
  "availabilityState": "Normal",
  "clientAffinityEnabled": false,
  "clientCertEnabled": false,
  "clientCertExclusionPaths": null,
  "clientCertMode": "Required",
  "cloningInfo": null,
  "containerSize": 0,
  "customDomainVerificationId": "5D1FE143704756870E93FE00437C5DF54F2E9043E93C8C4748E63838A9A36D31",
  "dailyMemoryTimeQuota": 0,
  "daprConfig": null,
  "defaultHostName": "webapp-webappphpmysql3.azurewebsites.net",
  "enabled": true,
  "enabledHostNames": [
    "webapp-webappphpmysql3.azurewebsites.net",
    "webapp-webappphpmysql3.scm.azurewebsites.net"
  ],
  "extendedLocation": null,
  "ftpPublishingUrl": "ftp://waws-prod-am2-805.ftp.azurewebsites.windows.net/site/wwwroot",
  "hostNameSslStates": [
    {
      "certificateResourceId": null,
      "hostType": "Standard",
      "ipBasedSslResult": null,
      "ipBasedSslState": "NotConfigured",
      "name": "webapp-webappphpmysql3.azurewebsites.net",
      "sslState": "Disabled",
      "thumbprint": null,
      "toUpdate": null,
      "toUpdateIpBasedSsl": null,
      "virtualIPv6": null,
      "virtualIp": null
    },
    {
      "certificateResourceId": null,
      "hostType": "Repository",
      "ipBasedSslResult": null,
      "ipBasedSslState": "NotConfigured",
      "name": "webapp-webappphpmysql3.scm.azurewebsites.net",
      "sslState": "Disabled",
      "thumbprint": null,
      "toUpdate": null,
      "toUpdateIpBasedSsl": null,
      "virtualIPv6": null,
      "virtualIp": null
    }
  ],
  "hostNames": [
    "webapp-webappphpmysql3.azurewebsites.net"
  ],
  "hostNamesDisabled": false,
  "hostingEnvironmentProfile": null,
  "httpsOnly": false,
  "hyperV": false,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webappphpmysql/providers/Microsoft.Web/sites/webapp-webappphpmysql3",
  "identity": {
    "principalId": "a081e3e8-9ec1-4c6a-bcc2-4074acec0fa4",
    "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409",
    "type": "SystemAssigned",
    "userAssignedIdentities": null
  },
  "inProgressOperationId": null,
  "isDefaultContainer": null,
  "isXenon": false,
  "keyVaultReferenceIdentity": "SystemAssigned",
  "kind": "app,linux",
  "lastModifiedTimeUtc": "2024-09-23T21:21:27.623333",
  "location": "West Europe",
  "managedEnvironmentId": null,
  "maxNumberOfWorkers": null,
  "name": "webapp-webappphpmysql3",
  "outboundIpAddresses": "108.142.57.96,108.142.57.101,108.142.57.102,108.142.57.104,108.142.57.108,108.142.57.113,108.142.56.229,108.142.56.235,108.142.56.254,108.142.57.0,108.142.57.14,108.142.57.38,20.105.224.48",
  "possibleOutboundIpAddresses": "108.142.56.229,108.142.56.235,108.142.56.254,108.142.57.0,108.142.57.101,108.142.57.102,108.142.57.104,108.142.57.108,108.142.57.113,108.142.57.116,108.142.57.125,108.142.57.131,108.142.57.132,108.142.57.134,108.142.57.138,108.142.57.14,108.142.57.38,108.142.57.65,108.142.57.67,108.142.57.68,108.142.57.71,108.142.57.73,108.142.57.74,108.142.57.76,108.142.57.79,108.142.57.80,108.142.57.87,108.142.57.90,108.142.57.92,108.142.57.96,20.105.224.48",
  "publicNetworkAccess": "Enabled",
  "redundancyMode": "None",
  "repositorySiteName": "webapp-webappphpmysql3",
  "reserved": true,
  "resourceConfig": null,
  "resourceGroup": "rg-webappphpmysql",
  "scmSiteAlsoStopped": false,
  "siteConfig": {
    "acrUseManagedIdentityCreds": false,
    "acrUserManagedIdentityId": null,
    "alwaysOn": false,
    "apiDefinition": null,
    "apiManagementConfig": null,
    "appCommandLine": "",
    "appSettings": null,
    "autoHealEnabled": false,
    "autoHealRules": null,
    "autoSwapSlotName": null,
    "azureStorageAccounts": {},
    "connectionStrings": null,
    "cors": null,
    "defaultDocuments": [
      "Default.htm",
      "Default.html",
      "Default.asp",
      "index.htm",
      "index.html",
      "iisstart.htm",
      "default.aspx",
      "index.php",
      "hostingstart.html"
    ],
    "detailedErrorLoggingEnabled": false,
    "documentRoot": null,
    "elasticWebAppScaleLimit": 0,
    "experiments": {
      "rampUpRules": []
    },
    "ftpsState": "Disabled",
    "functionAppScaleLimit": null,
    "functionsRuntimeScaleMonitoringEnabled": false,
    "handlerMappings": null,
    "healthCheckPath": null,
    "http20Enabled": false,
    "httpLoggingEnabled": false,
    "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webappphpmysql/providers/Microsoft.Web/sites/webapp-webappphpmysql3/config/web",
    "ipSecurityRestrictions": [
      {
        "action": "Allow",
        "description": "Allow all access",
        "headers": null,
        "ipAddress": "Any",
        "name": "Allow all",
        "priority": 2147483647,
        "subnetMask": null,
        "subnetTrafficTag": null,
        "tag": null,
        "vnetSubnetResourceId": null,
        "vnetTrafficTag": null
      }
    ],
    "ipSecurityRestrictionsDefaultAction": "Allow",
    "javaContainer": null,
    "javaContainerVersion": null,
    "javaVersion": null,
    "keyVaultReferenceIdentity": null,
    "kind": null,
    "limits": null,
    "linuxFxVersion": "PHP|8.1",
    "loadBalancing": "LeastRequests",
    "localMySqlEnabled": false,
    "location": "West Europe",
    "logsDirectorySizeLimit": 35,
    "machineKey": null,
    "managedPipelineMode": "Integrated",
    "managedServiceIdentityId": 9896,
    "metadata": null,
    "minTlsCipherSuite": null,
    "minTlsVersion": "1.2",
    "minimumElasticInstanceCount": 0,
    "name": "webapp-webappphpmysql3",
    "netFrameworkVersion": "v4.0",
    "nodeVersion": "",
    "numberOfWorkers": 1,
    "phpVersion": "",
    "powerShellVersion": "",
    "preWarmedInstanceCount": 0,
    "publicNetworkAccess": "Enabled",
    "publishingUsername": "$webapp-webappphpmysql3",
    "push": null,
    "pythonVersion": "",
    "remoteDebuggingEnabled": false,
    "remoteDebuggingVersion": "VS2022",
    "requestTracingEnabled": false,
    "requestTracingExpirationTime": null,
    "resourceGroup": "rg-webappphpmysql",
    "scmIpSecurityRestrictions": [
      {
        "action": "Allow",
        "description": "Allow all access",
        "headers": null,
        "ipAddress": "Any",
        "name": "Allow all",
        "priority": 2147483647,
        "subnetMask": null,
        "subnetTrafficTag": null,
        "tag": null,
        "vnetSubnetResourceId": null,
        "vnetTrafficTag": null
      }
    ],
    "scmIpSecurityRestrictionsDefaultAction": "Allow",
    "scmIpSecurityRestrictionsUseMain": false,
    "scmMinTlsVersion": "1.2",
    "scmType": "None",
    "tracingOptions": null,
    "type": "Microsoft.Web/sites/config",
    "use32BitWorkerProcess": true,
    "virtualApplications": [
      {
        "physicalPath": "site\\wwwroot",
        "preloadEnabled": false,
        "virtualDirectories": null,
        "virtualPath": "/"
      }
    ],
    "vnetName": "",
    "vnetPrivatePortsCount": 0,
    "vnetRouteAllEnabled": false,
    "webSocketsEnabled": false,
    "websiteTimeZone": null,
    "windowsFxVersion": null,
    "xManagedServiceIdentityId": null
  },
  "slotSwapStatus": null,
  "state": "Running",
  "storageAccountRequired": false,
  "suspendedTill": null,
  "tags": null,
  "targetSwapSlot": null,
  "trafficManagerHostNames": null,
  "type": "Microsoft.Web/sites",
  "usageState": "Normal",
  "virtualNetworkSubnetId": null,
  "vnetContentShareEnabled": false,
  "vnetImagePullEnabled": false,
  "vnetRouteAllEnabled": false,
  "workloadProfileName": null
}
```


---

I will follow now your advice and try to to create a Managed User Identity and use that one and check if it works.


```bash
az webapp show \
>     --resource-group rg-webappphpmysql \
>     --name webapp-webappphpmysql3 \
>     --query "{name: name, systemIdentityEnabled: identity.type == 'SystemAssigned' || identity.type == 'SystemAssigned, UserAssigned', userAssignedIdentities: identity.userAssignedIdentities}"
```

result

```
{
  "name": "webapp-webappphpmysql3",
  "systemIdentityEnabled": false,
  "userAssignedIdentities": {
    "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webappphpmysql/providers/Microsoft.ManagedIdentity/userAssignedIdentities/webappphpmysql3": {
      "clientId": "93ef3fa2-83a9-4f01-b605-8915590e7831",
      "principalId": "fcd1a4ed-e367-477c-bb1b-f988dfbc18d0"
    }
  }
}
```


```bash
az keyvault show \
    --name  kv-webappphpmysql3 \
    --resource-group rg-webappphpmysql \
    --query "properties.accessPolicies[?objectId=='fcd1a4ed-e367-477c-bb1b-f988dfbc18d0']"
```
output:

```json
[
  {
    "applicationId": null,
    "objectId": "fcd1a4ed-e367-477c-bb1b-f988dfbc18d0",
    "permissions": {
      "certificates": [],
      "keys": [],
      "secrets": [
        "get"
      ],
      "storage": null
    },
    "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409"
  }
]
```

az identity show \
    --ids "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webappphpmysql/providers/Microsoft.ManagedIdentity/userAssignedIdentities/webappphpmysql3"

```json
{
  "clientId": "93ef3fa2-83a9-4f01-b605-8915590e7831",
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webappphpmysql/providers/Microsoft.ManagedIdentity/userAssignedIdentities/webappphpmysql3",
  "location": "westeurope",
  "name": "webappphpmysql3",
  "principalId": "fcd1a4ed-e367-477c-bb1b-f988dfbc18d0",
  "resourceGroup": "rg-webappphpmysql",
  "systemData": null,
  "tags": {},
  "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409",
  "type": "Microsoft.ManagedIdentity/userAssignedIdentities"
}
```
