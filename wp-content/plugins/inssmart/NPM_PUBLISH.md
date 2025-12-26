# Инструкция по публикации в npm

## Подготовка к публикации

1. **Обновите версию в `package.json`**
   ```bash
   npm version patch  # для патч-версии (1.0.0 -> 1.0.1)
   npm version minor  # для минорной версии (1.0.0 -> 1.1.0)
   npm version major  # для мажорной версии (1.0.0 -> 2.0.0)
   ```

2. **Заполните поля в `package.json`**:
   - `author` - имя автора
   - `repository.url` - URL репозитория (если есть)
   - `license` - лицензия (уже установлена MIT)

3. **Соберите все версии**:
   ```bash
   npm run build:all
   ```

   Это создаст:
   - `dist/inssmart-form.js` и `dist/inssmart-form.css` - для WordPress
   - `dist/iframe/index.html` и связанные файлы - для iframe
   - `dist/index.d.ts` - файл типов TypeScript

## Публикация

### ⚠️ Важно: Требуется двухфакторная аутентификация (2FA)

npm требует включенную 2FA для публикации пакетов. Это обязательное требование безопасности.

### Настройка 2FA

1. **Войдите на npmjs.com** и перейдите в настройки:
   - Откройте https://www.npmjs.com/settings/YOUR_USERNAME/security
   - Или: Settings → Access Tokens → Enable 2FA

2. **Включите 2FA**:
   - Выберите метод: через приложение-аутентификатор (рекомендуется) или SMS
   - Сканируйте QR-код в приложении (Google Authenticator, Authy и т.д.)
   - Введите код подтверждения

3. **Для публикации пакетов** выберите режим 2FA:
   - **Authorization only** - только для авторизации (рекомендуется)
   - **Authorization and publishing** - для авторизации и публикации

### Публикация пакета

1. **Войдите в npm** (если еще не вошли):
   ```bash
   npm login
   ```
   
   При включенной 2FA вам потребуется ввести одноразовый код из приложения-аутентификатора.

2. **Проверьте, что вы вошли**:
   ```bash
   npm whoami
   ```

3. **Опубликуйте пакет**:
   ```bash
   npm publish
   ```
   
   При включенной 2FA в режиме "Authorization and publishing" может потребоваться код подтверждения.

   Скрипт `prepublishOnly` автоматически выполнит сборку перед публикацией.

4. **Для публикации в публичный registry** (если используете приватный):
   ```bash
   npm publish --access public
   ```

### Альтернатива: Использование токена доступа

Если вы не хотите использовать 2FA при каждой публикации, можно создать токен доступа:

1. **Создайте токен на npmjs.com**:
   - Перейдите: https://www.npmjs.com/settings/YOUR_USERNAME/access-tokens
   - Нажмите "Generate New Token"
   - Выберите тип: **Automation** (для CI/CD) или **Publish** (для публикации)
   - Скопируйте токен (он показывается только один раз!)

2. **Используйте токен для входа**:
   ```bash
   npm login --auth-type=legacy
   ```
   
   Введите:
   - Username: ваш npm username
   - Password: токен доступа (не пароль!)
   - Email: ваш email

3. **Или используйте токен напрямую**:
   ```bash
   echo "//registry.npmjs.org/:_authToken=YOUR_TOKEN" > ~/.npmrc
   ```

   **⚠️ Внимание:** Не коммитьте файл `.npmrc` с токеном в репозиторий!

## Использование после публикации

### Установка пакета:
```bash
npm install inssmart-form
```

### Использование WordPress версии:
```html
<link rel="stylesheet" href="node_modules/inssmart-form/dist/inssmart-form.css">
<div id="inssmart-form-container"></div>
<script src="node_modules/inssmart-form/dist/inssmart-form.js"></script>
<script>
  if (typeof window.initInssmartForm === 'function') {
    window.initInssmartForm('inssmart-form-container');
  }
</script>
```

### Использование iframe версии:
```html
<iframe 
  src="node_modules/inssmart-form/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
  style="border: none;"
></iframe>
```

### Использование через CDN (unpkg):
```html
<!-- WordPress версия -->
<link rel="stylesheet" href="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.css">
<script src="https://unpkg.com/inssmart-form@latest/dist/inssmart-form.js"></script>

<!-- iframe версия -->
<iframe 
  src="https://unpkg.com/inssmart-form@latest/dist/iframe/index.html"
  width="100%"
  height="800"
  frameborder="0"
></iframe>
```

## Обновление версии

После внесения изменений:

1. Обновите версию: `npm version patch|minor|major`
2. Соберите проект: `npm run build:all`
3. Опубликуйте: `npm publish`

## Отзыв пакета (если нужно)

Если опубликовали ошибочную версию, можно отозвать её в течение 72 часов:

```bash
npm unpublish inssmart-form@1.0.0
```

После 72 часов отзыв невозможен, нужно публиковать новую версию.


