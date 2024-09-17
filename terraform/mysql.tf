# Generate random value for the name
resource "random_string" "name" {
  length  = 8
  lower   = true
  numeric = false
  special = false
  upper   = false
}

output "name" {
  value = random_string.name.result
  sensitive = false
  
}

# Generate random value for the login password
resource "random_password" "password" {
  length           = 8
  lower            = true
  min_lower        = 1
  min_numeric      = 1
  min_special      = 1
  min_upper        = 1
  numeric          = true
  override_special = "_"
  special          = true
  upper            = true
}

output "password" {
  value = random_password.password.result
  sensitive = true
}


resource "azurerm_virtual_network" "dbvirtualnetwork" {
  name                = "db-vnet-${random_string.name.result}"
  location            = var.location
  resource_group_name = local.resource_group_name
  address_space       = ["10.0.0.0/16"]
}

resource "azurerm_subnet" "dbsubnet" {
  name                 = "db-subnet-${random_string.name.result}"
  resource_group_name  = local.resource_group_name
  virtual_network_name = azurerm_virtual_network.dbvirtualnetwork.name
  address_prefixes     = ["10.0.2.0/24"]
  delegation {
    name = "fs"
    service_delegation {
      name    = "Microsoft.DBforMySQL/flexibleServers"
      actions = ["Microsoft.Network/virtualNetworks/subnets/join/action"]
    }
  }
}

resource "azurerm_mysql_flexible_server" "dbserver" {
  name                = "db-server-${random_string.name.result}"
  location            = var.location
  resource_group_name = local.resource_group_name
  administrator_login    = "mysqladmin"
  administrator_password = random_password.password.result
  sku_name            = "B_Standard_B1ms"

}
