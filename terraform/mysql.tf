
# generate a username for the MySQL server and start with a letter
resource "random_string" "mysql_username" {
  length = 15
  special = false
  upper = false
}
# generate a password for the MySQL server
resource "random_password" "mysql_password" {
  length           = 16
  special          = true
  override_special = "_-"
}

locals {
  mysql_username = "u${random_string.mysql_username.result}"
  mysql_password = random_password.mysql_password.result
  depends_on = [ random_password.mysql_password , random_string.mysql_username ]
}
output "mysql_username" {
  value = local.mysql_username
}

output "mysql_password" {
  value = local.mysql_password
  sensitive = true
}

resource "azurerm_mysql_flexible_server" "mysql" {
  name                   = "mysql-${var.project_name}"
  resource_group_name    = var.resource_group_name
  location               = var.location
  administrator_login    = local.mysql_username
  administrator_password = local.mysql_password
  backup_retention_days  = 7
  sku_name               = "B_Standard_B1ms"
  version                = "8.0.21"
  zone                   = 2
  geo_redundant_backup_enabled = false

  depends_on = [ local.mysql_username, local.mysql_password ]

}

# enable access from within the Azure network
resource "azurerm_mysql_flexible_server_firewall_rule" "allow_azure_network" {
  name                = "AllowAzureNetwork"
  resource_group_name = var.resource_group_name
  server_name         = azurerm_mysql_flexible_server.mysql.name
  start_ip_address    = "0.0.0.0"
  end_ip_address      = "0.0.0.0"

  depends_on = [ azurerm_mysql_flexible_server.mysql ]
}

# create a database name in the MySQL server
locals {
  database_name = "db-${var.project_name}"
} 

# create a database in the MySQL server
resource "azurerm_mysql_flexible_database" "database" {
  name                = local.database_name
  resource_group_name = var.resource_group_name
  server_name         = azurerm_mysql_flexible_server.mysql.name
  charset             = "utf8mb3"
  collation           = "utf8mb3_unicode_ci"

  depends_on = [ azurerm_mysql_flexible_server_firewall_rule.allow_azure_network ]
}

# create a hello world table in the database for testing purposes
resource "null_resource" "hello_world_table" {
  provisioner "local-exec" {
    command = <<EOF
      mysql \
        -h ${azurerm_mysql_flexible_server.mysql.fqdn} \
        -u ${local.mysql_username} \
        -p${local.mysql_password} \
        ${azurerm_mysql_flexible_database.database.name} \
        -e "CREATE TABLE person ( \
                  firstname VARCHAR(50) NOT NULL, \
                  lastname  VARCHAR(50) NOT NULL, \
                  sex       ENUM('M', 'F') NOT NULL, \
                  age       INT UNSIGNED \
                );"
    EOF
  }

  depends_on = [ azurerm_mysql_flexible_database.database ]
}

# add also some data to the table
resource "null_resource" "hello_world_data" {
  provisioner "local-exec" {
    command = <<EOF
      mysql \
        -h ${azurerm_mysql_flexible_server.mysql.fqdn} \
        -u ${local.mysql_username} \
        -p${local.mysql_password} \
        ${azurerm_mysql_flexible_database.database.name} \
        -e "INSERT INTO person (firstname, lastname, sex, age) VALUES \
                ('Olivia', 'Smith', 'F', 19), \
                ('Liam', 'Johnson', 'M', 21), \
                ('Emma', 'Williams', 'F', 23), \
                ('Noah', 'Jones', 'M', 25), \
                ('Ava', 'Brown', 'F', 27), \
                ('Oliver', 'Davis', 'M', 29), \
                ('Isabella', 'Miller', 'F', 31), \
                ('Elijah', 'Wilson', 'M', 33), \
                ('Sophia', 'Moore', 'F', 35), \
                ('Lucas', 'Taylor', 'M', 37), \
                ('Mia', 'Anderson', 'F', 39), \
                ('James', 'Thomas', 'M', 41), \
                ('Amelia', 'Jackson', 'F', 43), \
                ('Alexander', 'White', 'M', 45), \
                ('Harper', 'Harris', 'F', 47);"
    EOF
  }

  depends_on = [ null_resource.hello_world_table ]
}

