Feature: Добавление нового модуля

  Scenario: Создаем новый модуль, проверяем результат и удаляем его
    Given Открываем страницу "http://localhost"
    When  Создаем модуль с заголовком "Module title", ключевыми вопросами "key questions" и описанием "description"
    And   Проверяем модуль с заголовком "Module title", ключевыми вопросами "key questions" и описанием "description"
    Then  Удаляем модуль
    And   Проверяем удаленный модуль