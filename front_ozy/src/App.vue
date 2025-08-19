<script setup>
import { ref } from "vue";
// axios
import axios from "axios";
import { useRouter } from "vue-router";

const router = useRouter();

const newTodo = ref("");
const description = ref("");
const todos = ref([]);
const completedTodos = ref(null);
const editId = ref(null);
const editText = ref("");
const editDescription = ref("");

const checkedTodos = ref([]);

const completeTodos = () => {
  axios
    .post("http://127.0.0.1:8000/api/complete-all", {
      _method: "PUT",
      ids: checkedTodos.value,
    })
    .then((response) => {
      // ambil ulang data
      fetchTodos();

      checkedTodos.value = [];
      alert("Todos completed successfully!");
    })
    .catch((error) => {
      console.error("Error completing todos:", error);
    });
};

// Ambil data awal
axios
  .get("http://127.0.0.1:8000/api/todo")
  .then((response) => {
    // Pastikan sesuai struktur API
    todos.value = Array.isArray(response.data.data)
      ? response.data.data
      : response.data;
  })
  .catch((error) => console.error("Error fetching todos:", error));

function addTodo() {
  const title = newTodo.value.trim();
  if (!title) {
    console.warn("Todo title is empty.");
    return;
  }

  axios
    .post("http://127.0.0.1:8000/api/todo", {
      title,
      description: description.value,
    })
    .then((response) => {
      // Cek apakah responsenya ada di data.data atau langsung data
      const newItem = response.data.data || response.data;

      // Tambahkan ke array supaya langsung kelihatan
      todos.value.unshift(newItem);

      // Reset input
      newTodo.value = "";
      description.value = "";
    })
    .catch((error) => {
      console.error("Error adding todo:", error);
    });
}

function editTodo(todo) {
  editId.value = todo.id;
  editText.value = todo.title;
  editDescription.value = todo.description;
}

function updateTodo(id) {
  axios
    .put(`http://127.0.0.1:8000/api/todo/${id}`, {
      title: editText.value,
      description: editDescription.value,
    })
    .then((response) => {
      const index = todos.value.findIndex((todo) => todo.id === id);
      if (index !== -1) {
        todos.value[index] = response.data.data;
      }
      alert("Todo updated successfully!");
      cancelEdit();
    })
    .catch((error) => {
      console.error("Error updating todo:", error);
    });
}

function cancelEdit() {
  editId.value = null;
  editText.value = "";
}

function deleteTodo(id) {
  if (!confirm("Apakah Anda yakin ingin menghapus todo ini?")) {
    return;
  }

  axios
    .delete(`http://127.0.0.1:8000/api/todo/${id}`)
    .then(() => {
      todos.value = todos.value.filter((t) => t.id !== id);
      alert("Todo deleted successfully!");
    })
    .catch((error) => {
      console.error("Error deleting todo:", error);
    });
}

const completeTodo = (id) => {
  axios
    .post(`http://127.0.0.1:8000/api/todo/${id}/complete`, {
      _method: "PUT",
    })
    .then((response) => {
      fetchTodos();
      alert("Todo completed successfully!");
    })
    .catch((error) => {
      console.error("Error completing todo:", error);
    });
};
// filter pending todos
const pendingTodos = () => {
  axios
    .get("http://127.0.0.1:8000/api/todo?status=pending")
    .then((response) => {
      // ambil ulang data
      todos.value = Array.isArray(response.data.data)
        ? response.data.data
        : response.data;
    })
    .catch((error) => {
      console.error("Error fetching pending todos:", error);
    });
};

// Generic fetch todos function
const fetchTodos = (status = null) => {
  const url = status
    ? `http://127.0.0.1:8000/api/todo?status=${status}`
    : "http://127.0.0.1:8000/api/todo";

  axios
    .get(url)
    .then((response) => {
      todos.value = Array.isArray(response.data.data)
        ? response.data.data
        : response.data;
    })
    .catch((error) => {
      console.error(`Error fetching ${status || "all"} todos:`, error);
    });
};

function filterByStatus(status) {
  router.push({ path: "/", query: { status } });
  fetchTodos(status); // your existing function
}
</script>

<template>
  <div class="bg-white p-6">
    <!-- alert flash -->
    <h1 class="text-2xl font-bold mb-4 text-black">Todo Manager</h1>

    <!-- Form -->
    <form @submit.prevent="addTodo" class="flex gap-2 mb-6">
      <input
        v-model="newTodo"
        type="text"
        placeholder="Tulis todo..."
        class="flex-1 border rounded-xl px-3 py-2 focus:outline-none focus:ring focus:border-blue-400 text-black"
      />
      <textarea
        v-model="description"
        placeholder="Deskripsi..."
        class="flex-1 border rounded-xl px-3 py-2 focus:outline-none focus:ring focus:border-blue-400 text-black"
      ></textarea>
      <button
        type="submit"
        class="px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
      >
        Tambah
      </button>
    </form>

    <!-- button selesai -->
    <div class="gap-4 flex flex-wrap mb-4">
      <button
        @click="completeTodos"
        class="mb-4 px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700"
      >
        Tandai Selesai
      </button>

      <button
        @click="filterByStatus(null)"
        class="mb-4 px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700"
      >
        Get All
      </button>

      <!-- button pending -->
      <button
        @click="filterByStatus('pending')"
        class="mb-4 px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700"
      >
        Get Pending
      </button>
      <!-- get completed -->
      <button
        @click="filterByStatus('completed')"
        class="mb-4 px-4 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700"
      >
        Get Completed
      </button>
    </div>

    <!-- Table -->
    <table class="w-full border border-slate-200 rounded-lg overflow-hidden">
      <thead class="bg-slate-100 text-black">
        <tr>
          <th class="p-2 text-left">Check</th>
          <th class="p-2 text-left">No</th>
          <th class="p-2 text-left">Todo</th>
          <th class="p-2 text-left">Deskripsi</th>
          <th class="p-2 text-left">Status</th>

          <th class="p-2 text-center">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(todo, index) in todos"
          :key="todo.id"
          class="border-t text-black"
        >
          <td class="p-2">
            <input
              type="checkbox"
              class="form-checkbox"
              v-model="checkedTodos"
              :value="todo.id"
              v-if="todo.status === 'pending'"
            />
          </td>
          <td class="p-2">{{ index + 1 }}</td>
          <td class="p-2">
            <input
              v-if="editId === todo.id"
              v-model="editText"
              type="text"
              class="border rounded px-2 py-1 w-full"
            />
            <span v-else>{{ todo.title }}</span>
          </td>
          <td class="p-2">
            <span v-if="editId === todo.id">
              <input
                v-model="editDescription"
                type="text"
                class="border rounded px-2 py-1 w-full text-nowrap"
              />
            </span>
            <span v-else>{{ todo.description }}</span>
          </td>
          <td class="p-2">
            <span v-if="todo.status === 'completed'" class="text-green-600"
              >Selesai</span
            >
            <span v-else class="text-red-600">Belum Selesai</span>
          </td>
          <td class="p-2 text-center space-x-2 flex justify-center">
            <button
              v-if="editId === todo.id"
              @click="updateTodo(todo.id)"
              class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
            >
              Simpan
            </button>
            <button
              v-if="editId === todo.id"
              @click="cancelEdit"
              class="px-3 py-1 bg-slate-400 text-white rounded hover:bg-slate-500"
            >
              Batal
            </button>
            <button
              v-else
              @click="editTodo(todo)"
              class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600"
            >
              Edit
            </button>
            <!-- complete -->
            <button
              @click="completeTodo(todo.id)"
              v-show="todo.status === 'pending'"
              class="px-3 py-1 bg-green-600 text-white rounded hover:bg-green-700"
            >
              Selesai
            </button>
            <button
              @click="deleteTodo(todo.id)"
              class="px-3 py-1 bg-red-600 text-white rounded hover:bg-red-700"
            >
              Hapus
            </button>
          </td>
        </tr>
        <tr v-if="todos.length === 0">
          <td colspan="3" class="p-3 text-center text-slate-500">
            Belum ada todo.
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>
