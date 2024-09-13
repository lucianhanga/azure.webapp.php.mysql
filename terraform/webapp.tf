
locals {
    app_service_plan_name = "asp-${var.project_name}"
}

#
# create the service plan for the webapp
#
resource "azurerm_service_plan" "webapp_serviceplan" {
  name                = local.app_service_plan_name
  location            = var.location
  resource_group_name = local.resource_group_name
  os_type             = "Linux"
  sku_name            = "F1" # Free tier
}

locals {
    webapp_name = "webapp-${var.project_name}"
}

#create the webapp using the "azurerm_linux_web_app" resource
resource "azurerm_linux_web_app" "webapp" {
  name                = local.webapp_name
  location            = var.location
  resource_group_name = local.resource_group_name

  service_plan_id = azurerm_service_plan.webapp_serviceplan.id
  site_config {
    always_on = false
    application_stack {
        php_version = "8.1"
    }
  }
}


