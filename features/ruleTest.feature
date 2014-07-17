Feature: Создание нового правила

  Scenario: Создаем правило
    Given Открываем страницу "http://localhost/books.php"
    And   Создаем новый учебник
    Then  Создаем новый модуль
    And   Создаем правило с текстом "Rule description"
    Then  Проверяем последнее созданное правило с текстом "Rule description"