Feature:
  In order to import enriched products from my PIM to an e-commerce solution importing data by using only CSV files
  As a connector
  I want to create a CSV file using the Akeneo Public API

  Scenario: It creates a CSV files containing the product information
    Given the product big_boot categorized in summer_collection and winter_boots
    And another product small_boot without any category
    When I export these products from the API
    Then I have the following file:
    """
    identifier;categories
    big_boot;shoes,clothes
    small_boot;
    """
