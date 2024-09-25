
#
# create the service plan for the webapp
#
resource "azurerm_service_plan" "webapp_serviceplan" {
  name                = local.app_service_plan_name
  location            = var.location
  resource_group_name = var.resource_group_name
  os_type             = "Linux"
  sku_name            = "F1" # Free tier
}


#create the webapp using the "azurerm_linux_web_app" resource
resource "azurerm_linux_web_app" "webapp" {
  name                = local.webapp_name
  location            = var.location
  resource_group_name = var.resource_group_name

  service_plan_id = azurerm_service_plan.webapp_serviceplan.id
  site_config {
    always_on = false
    application_stack {
        php_version = "8.3"
    }
  }

  # enable the managed identity for the webapp
  identity {
      type = "SystemAssigned"
  }

  # app settings
  app_settings =  local.app_envionment_variables

  depends_on = [ azurerm_service_plan.webapp_serviceplan ]
}


