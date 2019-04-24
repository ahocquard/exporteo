Feature:
  In order to import enriched products from my PIM to an e-commerce solution importing data by using only CSV files
  As a connector
  I want to create a CSV file using the Akeneo Public API

  Scenario: It creates a CSV files containing the product information
    Given the product bimbamboum categorized in shoes and clothes
    And another product boumbambim without any category
    When I export these products from the API
    Then I have the following file:
    """
    identifier;categories
    bimbamboum;shoes,clothes
    boumbambim;
    """
