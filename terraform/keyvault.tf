locals {
  key_vault_name = "kv-${var.project_name}" # the name of the key vault
}


# create a key vault where to store the username and password for the MySQL server
resource "azurerm_key_vault" "key_vault" {
  name                = local.key_vault_name
  resource_group_name = var.resource_group_name
  location            = var.location
  tenant_id            = var.tenant_id
  sku_name            = "standard"
  soft_delete_retention_days = 7
  purge_protection_enabled   = false

  enable_rbac_authorization = true

  # make sure that the webapp is created before creating the key vault 
  #   so that we can give it access to the key vault
  depends_on = [ 
    azurerm_linux_web_app.webapp
    ]
}

resource "azurerm_role_assignment" "webapp" {
  scope                = azurerm_key_vault.key_vault.id
  role_definition_name = "Key Vault Secrets User" # read access to the key vault
  principal_id         = azurerm_linux_web_app.webapp.identity[0].principal_id

  depends_on = [ azurerm_key_vault.key_vault, azurerm_linux_web_app.webapp ]
}

# add the mysql username and password to the key vault
resource "azurerm_key_vault_secret" "mysql_username" {
  name         = "mysql-username"
  value        = local.mysql_username
  key_vault_id = azurerm_key_vault.key_vault.id

  # first make sure the MySQL server is created before adding the username to the key vault
  depends_on = [ 
    azurerm_key_vault.key_vault,
    local.mysql_username 
  ]
}

resource "azurerm_key_vault_secret" "mysql_password" {
  name         = "mysql-password"
  value        = local.mysql_password
  key_vault_id = azurerm_key_vault.key_vault.id

  # first make sure the MySQL server is created before adding the password to the key vault
  depends_on = [ 
    azurerm_key_vault.key_vault, 
    local.mysql_password 
  ]
}
