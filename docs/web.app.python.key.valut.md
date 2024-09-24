# Azure Web App using Python

## Create the Resource Group

```bash
# create the resource group
az group create --name rg-webapp-python-F1AA --location westeurope
```

output:

```json
{
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA",
  "location": "westeurope",
  "managedBy": null,
  "name": "rg-webapp-python-F1AA",
  "properties": {
    "provisioningState": "Succeeded"
  },
  "tags": null,
  "type": "Microsoft.Resources/resourceGroups"
}
```

## create the app service plan

```bash
# create the app service plan
az appservice plan create \
    --name asp-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --sku F1 \
    --is-linux
```

output:

```json
{
  "elasticScaleEnabled": false,
  "extendedLocation": null,
  "freeOfferExpirationTime": null,
  "geoRegion": "West Europe",
  "hostingEnvironmentProfile": null,
  "hyperV": false,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/serverfarms/asp-webapp-python-F1AA",
  "isSpot": false,
  "isXenon": false,
  "kind": "linux",
  "kubeEnvironmentProfile": null,
  "location": "westeurope",
  "maximumElasticWorkerCount": 1,
  "maximumNumberOfWorkers": 0,
  "name": "asp-webapp-python-F1AA",
  "numberOfSites": 0,
  "numberOfWorkers": 1,
  "perSiteScaling": false,
  "provisioningState": "Succeeded",
  "reserved": true,
  "resourceGroup": "rg-webapp-python-F1AA",
  "sku": {
    "capabilities": null,
    "capacity": 1,
    "family": "U",
    "locations": null,
    "name": "U13",
    "size": "U13",
    "skuCapacity": null,
    "tier": "LinuxFree"
  },
  "spotExpirationTime": null,
  "status": "Ready",
  "subscription": "44feaee5-c984-4c09-a02f-46c7d78ad294",
  "tags": null,
  "targetWorkerCount": 0,
  "targetWorkerSizeId": 0,
  "type": "Microsoft.Web/serverfarms",
  "workerTierName": null,
  "zoneRedundant": false
}
```

## create the web app

### get the runtime stack for python

```bash
az webapp list-runtimes --os-type linux | grep -i python
```

output:

```json
  "PYTHON:3.12",
  "PYTHON:3.11",
  "PYTHON:3.10",
  "PYTHON:3.9",
  "PYTHON:3.8",
```

### create the web app

```bash
# create the web app
az webapp create \
    --name webapp-python-F1AA \
    --plan asp-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --runtime "PYTHON:3.12"
```

output:

```json
{
  "availabilityState": "Normal",
  "clientAffinityEnabled": true,
  "clientCertEnabled": false,
  "clientCertExclusionPaths": null,
  "clientCertMode": "Required",
  "cloningInfo": null,
  "containerSize": 0,
  "customDomainVerificationId": "5D1FE143704756870E93FE00437C5DF54F2E9043E93C8C4748E63838A9A36D31",
  "dailyMemoryTimeQuota": 0,
  "daprConfig": null,
  "defaultHostName": "webapp-python-f1aa.azurewebsites.net",
  "enabled": true,
  "enabledHostNames": [
    "webapp-python-f1aa.azurewebsites.net",
    "webapp-python-f1aa.scm.azurewebsites.net"
  ],
  "extendedLocation": null,
  "ftpPublishingUrl": "ftps://waws-prod-am2-769.ftp.azurewebsites.windows.net/site/wwwroot",
  "hostNameSslStates": [
    {
      "certificateResourceId": null,
      "hostType": "Standard",
      "ipBasedSslResult": null,
      "ipBasedSslState": "NotConfigured",
      "name": "webapp-python-f1aa.azurewebsites.net",
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
      "name": "webapp-python-f1aa.scm.azurewebsites.net",
      "sslState": "Disabled",
      "thumbprint": null,
      "toUpdate": null,
      "toUpdateIpBasedSsl": null,
      "virtualIPv6": null,
      "virtualIp": null
    }
  ],
  "hostNames": [
    "webapp-python-f1aa.azurewebsites.net"
  ],
  "hostNamesDisabled": false,
  "hostingEnvironmentProfile": null,
  "httpsOnly": false,
  "hyperV": false,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/sites/webapp-python-F1AA",
  "identity": null,
  "inProgressOperationId": null,
  "isDefaultContainer": null,
  "isXenon": false,
  "keyVaultReferenceIdentity": "SystemAssigned",
  "kind": "app,linux",
  "lastModifiedTimeUtc": "2024-09-24T12:51:45.283333",
  "location": "West Europe",
  "managedEnvironmentId": null,
  "maxNumberOfWorkers": null,
  "name": "webapp-python-F1AA",
  "outboundIpAddresses": "20.101.188.174,20.101.189.236,20.101.190.64,20.101.190.255,20.101.191.10,20.101.191.37,20.86.209.248,20.86.210.26,20.86.211.60,20.86.211.198,20.86.212.16,20.86.212.165,20.105.232.49",
  "possibleOutboundIpAddresses": "20.101.188.174,20.101.189.236,20.101.190.64,20.101.190.255,20.101.191.10,20.101.191.37,20.86.209.248,20.86.210.26,20.86.211.60,20.86.211.198,20.86.212.16,20.86.212.165,20.86.212.206,20.86.213.153,20.86.215.112,20.86.215.141,20.101.184.31,20.101.185.92,20.101.186.189,20.101.187.75,20.101.187.78,20.101.187.82,20.101.188.20,20.101.188.22,20.101.188.174,20.101.189.236,20.101.190.64,20.101.190.255,20.101.191.10,20.101.191.37,20.101.191.66,20.101.191.74,20.101.191.82,20.101.191.118,20.101.191.178,20.23.48.160,20.105.232.49",
  "publicNetworkAccess": null,
  "redundancyMode": "None",
  "repositorySiteName": "webapp-python-F1AA",
  "reserved": true,
  "resourceConfig": null,
  "resourceGroup": "rg-webapp-python-F1AA",
  "scmSiteAlsoStopped": false,
  "serverFarmId": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/serverfarms/asp-webapp-python-F1AA",
  "siteConfig": {
    "acrUseManagedIdentityCreds": false,
    "acrUserManagedIdentityId": null,
    "alwaysOn": false,
    "antivirusScanEnabled": null,
    "apiDefinition": null,
    "apiManagementConfig": null,
    "appCommandLine": null,
    "appSettings": null,
    "autoHealEnabled": null,
    "autoHealRules": null,
    "autoSwapSlotName": null,
    "azureMonitorLogCategories": null,
    "azureStorageAccounts": null,
    "clusteringEnabled": false,
    "connectionStrings": null,
    "cors": null,
    "customAppPoolIdentityAdminState": null,
    "customAppPoolIdentityTenantState": null,
    "defaultDocuments": null,
    "detailedErrorLoggingEnabled": null,
    "documentRoot": null,
    "elasticWebAppScaleLimit": 0,
    "experiments": null,
    "fileChangeAuditEnabled": null,
    "ftpsState": null,
    "functionAppScaleLimit": null,
    "functionsRuntimeScaleMonitoringEnabled": null,
    "handlerMappings": null,
    "healthCheckPath": null,
    "http20Enabled": false,
    "http20ProxyFlag": null,
    "httpLoggingEnabled": null,
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
    "ipSecurityRestrictionsDefaultAction": null,
    "javaContainer": null,
    "javaContainerVersion": null,
    "javaVersion": null,
    "keyVaultReferenceIdentity": null,
    "limits": null,
    "linuxFxVersion": "",
    "loadBalancing": null,
    "localMySqlEnabled": null,
    "logsDirectorySizeLimit": null,
    "machineKey": null,
    "managedPipelineMode": null,
    "managedServiceIdentityId": null,
    "metadata": null,
    "minTlsCipherSuite": null,
    "minTlsVersion": null,
    "minimumElasticInstanceCount": 0,
    "netFrameworkVersion": null,
    "nodeVersion": null,
    "numberOfWorkers": 1,
    "phpVersion": null,
    "powerShellVersion": null,
    "preWarmedInstanceCount": null,
    "publicNetworkAccess": null,
    "publishingPassword": null,
    "publishingUsername": null,
    "push": null,
    "pythonVersion": null,
    "remoteDebuggingEnabled": null,
    "remoteDebuggingVersion": null,
    "requestTracingEnabled": null,
    "requestTracingExpirationTime": null,
    "routingRules": null,
    "runtimeADUser": null,
    "runtimeADUserPassword": null,
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
    "scmIpSecurityRestrictionsDefaultAction": null,
    "scmIpSecurityRestrictionsUseMain": null,
    "scmMinTlsCipherSuite": null,
    "scmMinTlsVersion": null,
    "scmSupportedTlsCipherSuites": null,
    "scmType": null,
    "sitePort": null,
    "sitePrivateLinkHostEnabled": null,
    "storageType": null,
    "supportedTlsCipherSuites": null,
    "tracingOptions": null,
    "use32BitWorkerProcess": null,
    "virtualApplications": null,
    "vnetName": null,
    "vnetPrivatePortsCount": null,
    "vnetRouteAllEnabled": null,
    "webSocketsEnabled": null,
    "websiteTimeZone": null,
    "winAuthAdminState": null,
    "winAuthTenantState": null,
    "windowsConfiguredStacks": null,
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

## create a user managed identity

```bash
# create the user managed identity
az identity create \
    --name id-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA
```

output:

```json
{
  "clientId": "76b6f67b-b274-4691-99ad-aa4a9e8ccad6",
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webapp-python-F1AA/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id-webapp-python-F1AA",
  "location": "westeurope",
  "name": "id-webapp-python-F1AA",
  "principalId": "4a4c1d22-ac6e-40d1-9dae-dc7b3bd8f789",
  "resourceGroup": "rg-webapp-python-F1AA",
  "systemData": null,
  "tags": {},
  "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409",
  "type": "Microsoft.ManagedIdentity/userAssignedIdentities"
}
```

## assign the user managed identity to the web app

### show the full resource id of the web app

```bash
az webapp show \
    --name  webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --query "id" \
    --output tsv
```

output:

```json
/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/sites/webapp-python-F1AA
```

### show the full resource id of the user managed identity

```bash 
az identity show \
    --name id-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --query "id" \
    --output tsv
```

output:

```json
/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webapp-python-F1AA/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id-webapp-python-F1AA
```

### assign the user managed identity to the web app

```bash 
# assign the user managed identity to the web app
az webapp identity assign \
    --name webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --identities "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webapp-python-F1AA/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id-webapp-python-F1AA"
```

output:

```json
{
  "principalId": null,
  "tenantId": null,
  "type": "UserAssigned",
  "userAssignedIdentities": {
    "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourcegroups/rg-webapp-python-F1AA/providers/Microsoft.ManagedIdentity/userAssignedIdentities/id-webapp-python-F1AA": {
      "clientId": "76b6f67b-b274-4691-99ad-aa4a9e8ccad6",
      "principalId": "4a4c1d22-ac6e-40d1-9dae-dc7b3bd8f789"
    }
  }
}
```
## create a key vault

```bash
# create the key vault
az keyvault create \
    --name kv-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --location westeurope
```

output:

```json
{
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA",
  "location": "westeurope",
  "name": "kv-webapp-python-F1AA",
  "properties": {
    "accessPolicies": [],
    "createMode": null,
    "enablePurgeProtection": null,
    "enableRbacAuthorization": null,
    "enabledForDeployment": null,
    "enabledForDiskEncryption": null,
    "enabledForTemplateDeployment": null,
    "enableSoftDelete": null,
    "enableVaultForVolumeEncryption": null,
    "networkAcls": null,
    "privateEndpointConnections": [],
    "provisioningState": "Succeeded",
    "sku": {
      "family": "A",
      "name": "standard"
    },
    "tenantId": "9ddff61d-1e0f-425a-9643-d8a7cd9ad409",
    "vaultUri": "https://kv-webapp-python-f1aa.vault.azure.net/"
  },
  "resourceGroup": "rg-webapp-python-F1AA",
  "tags": {},
  "type": "Microsoft.KeyVault/vaults"
}
```

## add the current user as key vault secrets officer

```bash
az role assignment create \
  --role "Key Vault Secrets Officer" \
  --assignee bdb5f133-03ff-41e9-a3e6-67061bddbc9a \
  --scope /subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-f1aa/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA
```

output

```json
{
  "condition": null,
  "conditionVersion": null,
  "createdBy": null,
  "createdOn": "2024-09-24T13:28:26.254021+00:00",
  "delegatedManagedIdentityResourceId": null,
  "description": null,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-f1aa/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA/providers/Microsoft.Authorization/roleAssignments/4cf849cc-a315-4394-b859-e789b26d9660",
  "name": "4cf849cc-a315-4394-b859-e789b26d9660",
  "principalId": "bdb5f133-03ff-41e9-a3e6-67061bddbc9a",
  "principalType": "User",
  "resourceGroup": "rg-webapp-python-f1aa",
  "roleDefinitionId": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/providers/Microsoft.Authorization/roleDefinitions/b86a8fe4-44ce-4948-aee5-eccb2c155cd7",
  "scope": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-f1aa/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA",
  "type": "Microsoft.Authorization/roleAssignments",
  "updatedBy": "bdb5f133-03ff-41e9-a3e6-67061bddbc9a",
  "updatedOn": "2024-09-24T13:28:26.499026+00:00"
}
```

## add the current user as key vault secrets officer

This is required to create a secret in the key vault

```bash
# get the current user object id in a variable
objectId=$(az ad signed-in-user show --query id --output tsv)
# add a role assignment as administrator to the key vault
az role assignment create \
    --role "Key Vault Secrets Officer" \
    --assignee $objectId \
    --scope "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA"
```

output

```json
{
  "condition": null,
  "conditionVersion": null,
  "createdBy": null,
  "createdOn": "2024-09-24T13:17:26.947657+00:00",
  "delegatedManagedIdentityResourceId": null,
  "description": null,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA/providers/Microsoft.Authorization/roleAssignments/4bfc4f91-d540-4877-9932-ce398f618b84",
  "name": "4bfc4f91-d540-4877-9932-ce398f618b84",
  "principalId": "bdb5f133-03ff-41e9-a3e6-67061bddbc9a",
  "principalType": "User",
  "resourceGroup": "rg-webapp-python-F1AA",
  "roleDefinitionId": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/providers/Microsoft.Authorization/roleDefinitions/f25e0fa2-a7c8-4377-a976-54943a77a395",
  "scope": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA",
  "type": "Microsoft.Authorization/roleAssignments",
  "updatedBy": "bdb5f133-03ff-41e9-a3e6-67061bddbc9a",
  "updatedOn": "2024-09-24T13:17:27.187661+00:00"
}
```

## create a secret in the key vault

```bash
# list the secrets in the key vault
az keyvault secret list \
    --vault-name kv-webapp-python-F1AA

# create a secret in the key vault
az keyvault secret set \
    --vault-name kv-webapp-python-F1AA \
    --name "secret1" \
    --value "mysecret"  
```

output:

```json
{
  "attributes": {
    "created": "2024-09-24T13:30:35+00:00",
    "enabled": true,
    "expires": null,
    "notBefore": null,
    "recoverableDays": 90,
    "recoveryLevel": "Recoverable+Purgeable",
    "updated": "2024-09-24T13:30:35+00:00"
  },
  "contentType": null,
  "id": "https://kv-webapp-python-f1aa.vault.azure.net/secrets/secret1/d3818a3478434c4aa1ec313a0c3c94d7",
  "kid": null,
  "managed": null,
  "name": "secret1",
  "tags": {
    "file-encoding": "utf-8"
  },
  "value": "mysecret"
}
```

## assign the key vault access policy to the user managed identity

```bash
# get the user managed identity object id in a variable
objectId=$(az identity show \
    --name id-webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --query principalId \
    --output tsv)
echo $objectId
# assign to the user managed the key vault secret user role
az role assignment create \
    --role "Key Vault Secrets User" \
    --assignee $objectId \
    --scope "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA"
```

output:

```json
{
  "condition": null,
  "conditionVersion": null,
  "createdBy": null,
  "createdOn": "2024-09-24T13:36:34.246304+00:00",
  "delegatedManagedIdentityResourceId": null,
  "description": null,
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA/providers/Microsoft.Authorization/roleAssignments/c2fe6c4e-cc31-4994-91bb-06dc3de90251",
  "name": "c2fe6c4e-cc31-4994-91bb-06dc3de90251",
  "principalId": "4a4c1d22-ac6e-40d1-9dae-dc7b3bd8f789",
  "principalType": "ServicePrincipal",
  "resourceGroup": "rg-webapp-python-F1AA",
  "roleDefinitionId": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/providers/Microsoft.Authorization/roleDefinitions/4633458b-17de-408a-b874-0445c86b69e6",
  "scope": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.KeyVault/vaults/kv-webapp-python-F1AA",
  "type": "Microsoft.Authorization/roleAssignments",
  "updatedBy": "bdb5f133-03ff-41e9-a3e6-67061bddbc9a",
  "updatedOn": "2024-09-24T13:36:34.487305+00:00"
}
```

# Create the azure python web app

## create the folder and the virtual environment and activate it

```bash
mkdir azure-python-webapp
cd azure-python-webapp
python --version
python -m venv venv
source venv/bin/activate
```

## install flask 

```bash
pip install flask
```

## create the app

write in the file `app.py` the following code:

```python
from flask import Flask

app = Flask(__name__)

@app.route('/')
def hello_world():
    return 'Hello, Azure!'

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)
```

## create the requirements file

```bash
pip freeze > requirements.txt
```

# Deploy the app to azure

Deploy using the zip deployment

```bash
# create the zip fil
zip -r app.zip app.py requirements.txt
# deploy the zip file
az webapp deploy \
    --name webapp-python-F1AA \
    --resource-group rg-webapp-python-F1AA \
    --src-path app.zip
```

output:

```
Deployment type: zip. To override deployment type, please specify the --type parameter. Possible values: war, jar, ear, zip, startup, script, static
Initiating deployment
Deploying from local path: app.zip

Polling the status of sync deployment. Start Time: 2024-09-24 13:53:10.380612+00:00 UTC
Status: Build successful. Time: 0(s)
Status: Site started successfully. Time: 15(s)
Deployment has completed successfully
You can visit your app at: http://webapp-python-f1aa.azurewebsites.net
```

```json
{
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/sites/webapp-python-F1AA/deploymentStatus/43925f30-e8a2-4b48-9a01-37fa58fd33d8",
  "location": "West Europe",
  "name": "43925f30-e8a2-4b48-9a01-37fa58fd33d8",
  "properties": {
    "deploymentId": "43925f30-e8a2-4b48-9a01-37fa58fd33d8",
    "errors": null,
    "failedInstancesLogs": null,
    "numberOfInstancesFailed": 0,
    "numberOfInstancesInProgress": 0,
    "numberOfInstancesSuccessful": 1,
    "status": "RuntimeSuccessful"
  },
  "resourceGroup": "rg-webapp-python-F1AA",
  "type": "Microsoft.Web/sites/deploymentStatus"
}
```

 ***all good !***

# Make the app get the secret from the key vault

## install the azure identity library

```bash
pip install azure-identity
```

## write the code to get the secret from the key vault

write in the file `app.py` the following code:

```python
from flask import Flask
from azure.identity import DefaultAzureCredential
from azure.keyvault.secrets import SecretClient
from azure.core.exceptions import AzureError

app = Flask(__name__)
@app.route('/')

def hello_world():
    """
    Function to fetch a secret from Azure Key Vault and return a greeting message.
    This function demonstrates:
    - Establishing a connection to Azure Key Vault using Azure credentials.
    - Retrieving a secret stored in the Key Vault.
    - Returning a formatted message including the secret value.
    """

    # Initialize the DefaultAzureCredential object.
    # This credential attempts to use multiple authentication methods in this order:
    # - Environment variables
    # - Managed Identity
    # - Visual Studio Code or Azure CLI credentials
    try:
        credential = DefaultAzureCredential()
    except AzureError as auth_error:
        # If there is an error in authentication, return an error message.
        return f"Authentication error: {auth_error}"

    # Define the Key Vault URL. Replace with your actual Key Vault URL if different.
    vault_url = "https://kv-webapp-python-f1aa.vault.azure.net/"

    try:
        # Create a SecretClient using the provided credential and Key Vault URL.
        secret_client = SecretClient(vault_url=vault_url, credential=credential)
    except AzureError as client_error:
        # If there is an error in creating the SecretClient, return an error message.
        return f"Failed to create SecretClient: {client_error}"

    # Define the secret name to be retrieved from the Key Vault.
    secret_name = "secret1"

    try:
        # Attempt to retrieve the secret value from the Key Vault.
        secret = secret_client.get_secret(secret_name)
    except AzureError as secret_error:
        # If there is an error in retrieving the secret, return an error message.
        return f"Failed to retrieve the secret '{secret_name}': {secret_error}"

    # If everything goes well, format and return the greeting message including the secret value.
    # Ensure that sensitive information is handled appropriately and avoid logging secrets in production environments.
    return f"Hello, Azure! The secret is: {secret.value}"
    
if __name__ == '__main__':
    app.run(host='0.0.0.0', port=8080)
```

## install the packages and freeze the requirements

```bash
pip install azure-identity
pip install azure-keyvault-secrets
pip install azure-core
pip freeze > requirements.txt
```

## redeploy the app

```bash
# create the zip fil
zip -r app.zip app.py requirements.txt
# deploy the zip file
az webapp deploy \
    --name  testlucian123\
    --resource-group appsvc_linux_centralus \
    --type zip \
    --src-path app.zip
```

output:

```json
Deployment type: zip. To override deployment type, please specify the --type parameter. Possible values: war, jar, ear, zip, startup, script, static
Initiating deployment
Deploying from local path: app.zip



Polling the status of sync deployment. Start Time: 2024-09-24 14:14:28.621861+00:00 UTC
Status: Build successful. Time: 0(s)
Status: Site started successfully. Time: 15(s)
Deployment has completed successfully
You can visit your app at: http://webapp-python-f1aa.azurewebsites.net
{
  "id": "/subscriptions/44feaee5-c984-4c09-a02f-46c7d78ad294/resourceGroups/rg-webapp-python-F1AA/providers/Microsoft.Web/sites/webapp-python-F1AA/deploymentStatus/f3a1f337-27b2-4268-b6c8-2acf515afc46",
  "location": "West Europe",
  "name": "f3a1f337-27b2-4268-b6c8-2acf515afc46",
  "properties": {
    "deploymentId": "f3a1f337-27b2-4268-b6c8-2acf515afc46",
    "errors": null,
    "failedInstancesLogs": null,
    "numberOfInstancesFailed": 0,
    "numberOfInstancesInProgress": 0,
    "numberOfInstancesSuccessful": 1,
    "status": "RuntimeSuccessful"
  },
  "resourceGroup": "rg-webapp-python-F1AA",
  "type": "Microsoft.Web/sites/deploymentStatus"
}
```
