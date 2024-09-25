locals {
  key_vault_name        = "kv-${var.project_name}" # the name of the key vault
  app_service_plan_name = "asp-${var.project_name}"
  webapp_name           = "webapp-${var.project_name}"    
  mysql_server_name     = "mysql-${var.project_name}"
  database_name         = "db-${var.project_name}"
  app_envionment_variables = {
    KEYVAULT_NAME = local.key_vault_name
    KEYVAULT_SECRET_MYSQL_USERNAME = "mysql-username"
    KEYVAULT_SECRET_MYSQL_PASSWORD = "mysql-password"
    MYSQL_SERVER_NAME = local.mysql_server_name
    MYSQL_DATABASE_NAME = local.database_name
  }
}

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


