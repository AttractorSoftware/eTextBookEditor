Feature: Создание нового правила
  Scenario: Создаем правило
    Given Открываем страницу "http://localhost"
    When  Создаем модуль с заголовком "Module title", ключевыми вопросами "key questions" и описанием "description"
    And   Создаем правило с текстом "Rule description"
    Then  Проверяем последнее созданное правило с текстом "Rule description"