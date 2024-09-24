#!/bin/bash

# call it source ./terraform.login.bash

# Define the path to the terraform.tfvars file
TFVARS_FILE="terraform.tfvars"

# Function to extract a value from the terraform.tfvars file
extract_value() {
    grep -E "^$1[[:space:]]*=" "$TFVARS_FILE" | sed 's/.*=[[:space:]]*"\([^"]*\)".*/\1/'
}

# Extracting values from terraform.tfvars
CLIENT_ID=$(extract_value "client_id")
CLIENT_SECRET=$(extract_value "client_secret")
TENANT_ID=$(extract_value "tenant_id")
SUBSCRIPTION_ID=$(extract_value "subscription_id")

# Check if values are extracted correctly
if [ -z "$CLIENT_ID" ] || [ -z "$CLIENT_SECRET" ] || [ -z "$TENANT_ID" ] || [ -z "$SUBSCRIPTION_ID" ]; then
  echo "Error: Unable to extract one or more required values from terraform.tfvars."
  exit 1
fi

# Create a temporary JSON file with Azure credentials
AZURE_CREDENTIALS_JSON=$(mktemp)
cat <<EOF > "$AZURE_CREDENTIALS_JSON"
{
    "clientId": "$CLIENT_ID",
    "clientSecret": "$CLIENT_SECRET",
    "subscriptionId": "$SUBSCRIPTION_ID",
    "tenantId": "$TENANT_ID"
}
EOF

# Logging into Azure using the generated JSON file
echo "Logging into Azure..."
az login --service-principal --username "$CLIENT_ID" --password "$CLIENT_SECRET" --tenant "$TENANT_ID"

# Check if login was successful
if [ $? -eq 0 ]; then
  echo "Successfully logged into Azure."
else
  echo "Azure login failed."
fi

export ARM_CLIENT_ID=$CLIENT_ID
export ARM_CLIENT_SECRET=$CLIENT_SECRET
export ARM_SUBSCRIPTION_ID=$SUBSCRIPTION_ID
export ARM_TENANT_ID=$TENANT_ID

