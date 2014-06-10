Feature: Добавление нового блока

  Scenario: Создаем новый блок, проверяем результат и удаляем его
    Given Открываем страницу "http://localhost"
    When  Создаем модуль с заголовком "Module title", ключевыми вопросами "key questions" и описанием "description"
    And   Создаем блок с заголовком "Block title"
    Then  Проверяем блок с заголовком "Block title"
    And   Удаляем блок
    And   Проверяем удаленный блок
