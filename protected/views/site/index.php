<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>To-Do List</title>
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.js"></script>
  <style>
      body {
          font-family: Arial, sans-serif;
          margin: 20px;
          background-color: #f4f4f4;
      }

      #app {
          background-color: #fff;
          padding: 20px;
          border-radius: 8px;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
          max-width: 500px;
          margin: 0 auto;
      }

      h1 {
          text-align: center;
          color: #333;
      }

      ul {
          list-style: none;
          padding: 0;
      }

      li {
          background-color: #e9e9e9;
          margin-bottom: 8px;
          padding: 10px;
          border-radius: 4px;
          display: flex;
          justify-content: space-between;
          align-items: center;
      }

      li.done {
          text-decoration: line-through;
          color: #888;
          background-color: #f0f0f0;
      }

      .add-form {
          display: flex;
          margin-top: 20px;
      }

      .add-form input[type="text"] {
          flex-grow: 1;
          padding: 10px;
          border: 1px solid #ddd;
          border-radius: 4px;
          margin-right: 10px;
      }

      .add-form button {
          padding: 10px 15px;
          background-color: #007bff;
          color: white;
          border: none;
          border-radius: 4px;
          cursor: pointer;
      }

      .add-form button:hover {
          background-color: #0056b3;
      }

      .error {
          color: red;
          margin-top: 10px;
          font-weight: bold;
      }

      .details {
          color: #f55555;
          font-size: 0.9em;
          margin-top: 5px;
      }

      .spinner {
          border: 4px solid rgba(0, 0, 0, 0.1);
          border-left-color: #007bff;
          border-radius: 50%;
          width: 20px;
          height: 20px;
          animation: spin 1s linear infinite;
          display: inline-block;
          vertical-align: middle;
          margin-left: 10px;
      }

      @keyframes spin {
          to {
              transform: rotate(360deg);
          }
      }
  </style>
</head>
<body>
<div id="app">
  <h1>Список Задач</h1>

  <div class="add-form">
    <input type="text" v-model="newTaskTitle" @keyup.enter="addTask"
           placeholder="Добавить новую задачу..." :disabled="isLoading">
    <button @click="addTask" :disabled="isLoading">Добавить <span v-if="isLoading"
                                                                  class="spinner"></span></button>
  </div>
  <p v-if="error" class="error">{{ error }}</p>
  <div v-if="Object.keys(details).length > 0" class="details">
    <p v-for="(messages, field) in details" :key="field">
      <strong>{{ field }}:</strong> {{ messages.join(', ') }}
    </p>
  </div>

  <ul v-if="tasks.length">
    <li v-for="task in tasks" :key="task.id" :class="{ done: task.is_done }">
      {{ task.title }}
    </li>
  </ul>
  <p v-else-if="!isLoading">Задач пока нет! Добавьте первую.</p>
  <p v-else>Загрузка задач...</p>
</div>

<script>
  new Vue({
    el: '#app',
    data: {
      tasks: [],
      newTaskTitle: '',
      error: null,
      details: {}, // Добавляем объект для деталей ошибок валидации
      isLoading: false, // Флаг для отслеживания состояния загрузки

      apiUrl: '/api'
    },
    methods: {
      /**
       * API
       *
       * @param {string} endpoint Ендпоинт
       * @param {string} method HTTP-метод
       * @param {object} [data=null] Данные для отправки
       * @returns {Promise<any>}
       */
      async callApi(endpoint, method, data = null) {
        // Если уже идет загрузка, игнорируем новый запрос и выходим
        if (this.isLoading) {
          console.warn('Запрос игнорирован: уже идет загрузка.');
          throw new Error('Уже идет загрузка. Пожалуйста, подождите.');
        }

        this.isLoading = true;
        this.error = null;
        this.details = {};

        const url = `${this.apiUrl}${endpoint}`;
        const options = {
          method: method,
          headers: {
            'Content-Type': 'application/json'
          }
        };

        if (data) {
          options.body = JSON.stringify(data);
        }

        try {
          const response = await fetch(url, options);

          if (!response.ok) {
            const errorData = await response.json();
            const err = new Error(errorData.error || `Ошибка ${response.status}`);
            err.details = errorData.details;
            throw err;
          }

          return await response.json();
        } catch (error) {
          console.error(`Ошибка при вызове API (${method} ${endpoint}):`, error);
          this.error = `Ошибка: ${error.message}`;
          this.details = error.details || {};
          throw error;
        } finally {
          this.isLoading = false; // Снимаем флаг загрузки
        }
      },

      // --- Методы для работы с задачами ---
      async fetchTasks() {
        try {
          const data = await this.callApi('/list', 'GET');
          this.tasks = data;
        } catch (err) {
          this.tasks = []; // Очищаем список при ошибке загрузки
        }
      },

      async addTask() {
        if (!this.newTaskTitle.trim()) {
          this.error = 'Введите Название задачи';
          this.details = {};
          return;
        }

        const newTask = await this.callApi('/create', 'POST', {title: this.newTaskTitle});
        this.tasks.unshift(newTask);
        this.newTaskTitle = '';
      },

    },
    mounted() {
      this.fetchTasks(); // Загружаем задачи
    }
  });
</script>
</body>
</html>
