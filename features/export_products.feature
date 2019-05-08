Feature:
  In order to import enriched product data from my PIM to an e-commerce solution with CSV files
  I want to create a Product CSV file uwhen exporting products with the API

  Scenario: It creates a CSV files containing the product information
    Given the product big_boot categorized in summer_collection and winter_boots
    And another product docks_red categorized in winter_collection
    And another product small_boot without any category
    When I export these products from the API
    Then I have the following file:
    """
categories,identifier
"summer_collection,winter_boots",big_boot
winter_collection,docks_red
,small_boot

    """
