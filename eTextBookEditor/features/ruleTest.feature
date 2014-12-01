#noinspection CucumberUndefinedStep
Feature: Создание нового правила

  Scenario: Создаем правило
    Given Открываем страницу авторизации
    And   Авторизовываемся как администратор
    And   Кликаем по ссылке с классом "book-list-link"
    And   Создаем новый учебник
    Then  Создаем новый модуль
    And   Создаем правило с текстом "Rule description"
    Then  Проверяем последнее созданное правило с текстом "Rule description"