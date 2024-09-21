

resource "azurerm_mysql_flexible_server" "mysql" {
  name                   = "mysql-${var.project_name}"
  resource_group_name    = var.resource_group_name
  location               = var.location
  administrator_login    = "psqladmin"
  administrator_password = "H@Sh1CoR3!"
  backup_retention_days  = 7
  # delegated_subnet_id    = azurerm_subnet.subnet-mysql.id
  # private_dns_zone_id    = azurerm_private_dns_zone.priv-dns-mysql.id
  sku_name               = "B_Standard_B1ms"
  version                = "8.0.21"

  geo_redundant_backup_enabled = false

}

