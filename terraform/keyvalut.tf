locals {
  key_vault_name = "kv-${var.project_name}" # the name of the key vault
}

# get the object id of the current user - which is the service principal
data "azurerm_client_config" "current" {}


# create a key vault where to store the username and password for the MySQL server
resource "azurerm_key_vault" "key_vault" {
  name                = local.key_vault_name
  resource_group_name = var.resource_group_name
  location            = var.location
  tenant_id            = var.tenant_id
  sku_name            = "standard"

  access_policy {
    tenant_id = var.tenant_id
    object_id = "00000000-0000-0000-0000-000000000000" # the object id of the current user
    secret_permissions = ["Get", "List", "Set", "Delete", "Purge", "Recover"]
  }

  # # give access to the terraform service principal to the key vault
  # #   to add the username and password to the key vault for the MySQL server
  # access_policy {
  #   tenant_id = var.tenant_id
  #   object_id = data.azurerm_client_config.current.object_id
  #   secret_permissions = ["Get", "List", "Set", "Delete", "Purge", "Recover"]
  # }

  # give access to the webapp to read the username and password from the key vault
  access_policy {
    tenant_id = var.tenant_id
    object_id = azurerm_linux_web_app.webapp.identity[0].principal_id
    secret_permissions = ["Get"]
  }

  purge_protection_enabled = false # make the key vault deletable for development purposes

  # make sure that the webapp is created before creating the key vault 
  #   so that we can give it access to the key vault
  depends_on = [ azurerm_linux_web_app.webapp ]
}

# add the mysql username and password to the key vault
resource "azurerm_key_vault_secret" "mysql_username" {
  name         = "mysql-username"
  value        = local.mysql_username
  key_vault_id = azurerm_key_vault.key_vault.id

  # first make sure the MySQL server is created before adding the username to the key vault
  depends_on = [ 
    azurerm_mysql_flexible_server.mysql, 
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
    azurerm_mysql_flexible_server.mysql, 
    azurerm_key_vault.key_vault, 
    local.mysql_password 
  ]
}



