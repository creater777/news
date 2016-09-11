Новостной сайт на Yii2
---------------------------
Используется basic шаблон фреймворка Yii2. Новостной сайт с авторизацией и оповещением пользователей о событиях.

Основные возможности:
- Регистрация и авторизация пользователей с подтверждением почтового ящика.
- При добавлении новости на сайт, оповещение зарегистрированных пользователей по e-mail и всплывающим окном в браузере.
- Постраничный вывод превью новостей на главной странице с дальнейшим полным просмотром. Количество превью на странице изменяемо.
- CRUD управление новостями и пользователями с разграничением прав. Анонимный пользователь может просматривать только превью, пользователь может просматривать полные новости, модератор может добавлять новости, а администратор еще и управлять пользователями.
- В настройках профиля настройка уведомлений (получать уведомления о новых новостях только на e-mail, в браузер или и то и другое)
- Оповещение пользователя по e-mail при изменении пароля  или создания нового пользователя администратором (выслать новому пользователю на e-mail ссылку для активации профиля и ввода нового пароля для дальнейшей авторизации) и оповещать администратора при регистрации нового пользователя.
- Автоматическая авторизация на сайте при активации профиля.

В перспективе:
Добавить управление уведомлениями на основе системы событий Yii2 с следующими требованиями:
- Возможность добавления событий к любым моделям (тригерим события), отслеживание событий (слушаем события модели). 
- Возможность управления  уведомлениями к событиям из веб-интерфейса. С указанием в качестве адресата группу/роль пользователей и выбором типа уведомления (e-mail и/или браузер). Реализовать возможность управления шаблонами текстов уведомлений с автоподстановкой туда информации из уведомления. Например, подстановка имени пользователя или ссылки на появившуюся новость в тексте и заголовке уведомления.
- Предусмотреть возможность легкого добавления новых типов уведомлений. Например, в telegram или push (описать в readme как добавлять новые типы).
- Немедленная отправка уведомлений выбранным пользователям/ролям/всем по требованию администратора без события в модели.


Установка
---------
1. Установка
   ```
   git clone https://github.com/creater777/news
   ```
2. Инициализация приложения
   ```
   php E:\OpenServer\modules\php\PHP-5.4\composer.phar install
   ```

3. Инициализация БД
   ```
   php yii migrate/up
   ```

4. Инициализация rbac
   ```
   php yii migrate/up --migrationPath=@yii/rbac/migrations
   ```

5. Инициализация пользователей
   ```
   php yii rbac/init
   ```

PS
--
Разработка велась с параллельным изучением фреймворка, т.к. раньше с ним работать не приходилось. Приблизительно затраченно времени около полноценной рабочей недели с переработками ~60 часов.
Резюме https://yadi.sk/i/RBZOGzFev2EyB