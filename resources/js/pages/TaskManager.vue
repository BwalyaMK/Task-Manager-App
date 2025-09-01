<template>
  <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-purple-900">
    <!-- Header -->
    <div class="bg-gray-800 shadow-lg border-b border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-white">Task Manager</h1>
            <p class="text-gray-300 mt-1">Organize your tasks efficiently</p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-400">
              {{ stats.total }} tasks total
            </div>
            <button
              @click="showCategoryModal = true"
              class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition-colors"
            >
              + Category
            </button>
            <button
              @click="logout"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors flex items-center space-x-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
              </svg>
              <span>Logout</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Flash Messages -->
      <div v-if="flash?.success" class="mb-6 bg-green-800 border border-green-600 text-green-200 px-4 py-3 rounded relative">
        {{ flash.success }}
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
          <div class="flex items-center">
            <div class="p-2 bg-blue-900 rounded-lg">
              <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">Total Tasks</h3>
              <p class="text-2xl font-bold text-blue-400">{{ stats.total }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
          <div class="flex items-center">
            <div class="p-2 bg-yellow-900 rounded-lg">
              <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">Pending</h3>
              <p class="text-2xl font-bold text-yellow-400">{{ stats.pending }}</p>
            </div>
          </div>
        </div>

        <div class="bg-gray-800 rounded-xl shadow-lg p-6 border border-gray-700">
          <div class="flex items-center">
            <div class="p-2 bg-green-900 rounded-lg">
              <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
            </div>
            <div class="ml-4">
              <h3 class="text-lg font-semibold text-white">Completed</h3>
              <p class="text-2xl font-bold text-green-400">{{ stats.completed }}</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Add Task Form -->
      <div class="bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-4">Add New Task</h2>
        <form @submit.prevent="submit" class="space-y-4">
          <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
              <input
                v-model="form.title"
                type="text"
                placeholder="What needs to be done?"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': form.errors.title }"
              >
              <div v-if="form.errors.title" class="mt-1 text-sm text-red-400">
                {{ form.errors.title }}
              </div>
            </div>
            <div class="sm:w-48">
              <select
                v-model="form.category_id"
                class="w-full px-4 py-3 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">No Category</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                  {{ category.name }}
                </option>
              </select>
            </div>
            <button
              type="submit"
              :disabled="form.processing"
              class="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition-colors"
            >
              {{ form.processing ? 'Adding...' : 'Add Task' }}
            </button>
          </div>
        </form>
      </div>

      <!-- Filters -->
      <div class="bg-gray-800 rounded-xl shadow-lg p-6 mb-8 border border-gray-700">
        <h2 class="text-xl font-semibold text-white mb-4">Filters</h2>
        <div class="flex flex-col sm:flex-row gap-4">
          <div class="flex-1">
            <input
              v-model="search"
              type="text"
              placeholder="Search tasks..."
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
          </div>
          <div class="sm:w-48">
            <select
              v-model="selectedCategory"
              @change="applyFilters"
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Categories</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
          <div class="sm:w-48">
            <select
              v-model="selectedStatus"
              @change="applyFilters"
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Status</option>
              <option value="pending">Pending</option>
              <option value="completed">Completed</option>
            </select>
          </div>
          <button
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="px-4 py-2 text-gray-300 hover:text-white border border-gray-600 rounded-lg hover:bg-gray-700 transition-colors"
          >
            Clear Filters
          </button>
        </div>
      </div>

      <!-- Tasks List -->
      <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700">
        <div class="p-6 border-b border-gray-700">
          <h2 class="text-xl font-semibold text-white">Tasks</h2>
        </div>
        
        <div v-if="tasks.data.length === 0" class="p-8 text-center text-gray-400">
          <svg class="w-16 h-16 mx-auto mb-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
          </svg>
          <p class="text-lg font-medium">No tasks found</p>
          <p class="text-sm">Create your first task to get started!</p>
        </div>

        <div v-else class="divide-y divide-gray-700">
          <div
            v-for="task in tasks.data"
            :key="task.id"
            class="p-6 hover:bg-gray-750 transition-colors"
          >
            <div class="flex items-start justify-between">
              <div class="flex items-start space-x-3 flex-1">
                <button
                  @click="toggleTask(task)"
                  class="mt-1 flex-shrink-0"
                >
                  <div
                    class="w-5 h-5 rounded border-2 flex items-center justify-center transition-colors"
                    :class="task.completed ? 'bg-green-500 border-green-500' : 'border-gray-500 hover:border-green-500'"
                  >
                    <svg
                      v-if="task.completed"
                      class="w-3 h-3 text-white"
                      fill="none"
                      stroke="currentColor"
                      viewBox="0 0 24 24"
                    >
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                  </div>
                </button>

                <div class="flex-1 min-w-0">
                  <h3
                    class="text-lg font-medium transition-colors"
                    :class="task.completed ? 'text-gray-500 line-through' : 'text-white'"
                  >
                    {{ task.title }}
                  </h3>
                  <div class="flex items-center space-x-4 mt-2">
                    <span
                      v-if="task.category"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="{ backgroundColor: task.category.color + '40', color: task.category.color }"
                    >
                      {{ task.category.name }}
                    </span>
                    <span class="text-sm text-gray-400">
                      {{ new Date(task.created_at).toLocaleDateString() }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="flex items-center space-x-2 ml-4">
                <select
                  :value="task.category_id || ''"
                  @change="updateTaskCategory(task, ($event.target as HTMLSelectElement).value)"
                  class="text-sm bg-gray-700 border border-gray-600 text-white rounded px-2 py-1"
                >
                  <option value="">No Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                
                <button
                  @click="editTask(task)"
                  class="p-2 text-gray-400 hover:text-blue-400 transition-colors"
                  title="Edit task"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>

                <button
                  @click="deleteTask(task)"
                  class="p-2 text-gray-400 hover:text-red-400 transition-colors"
                  title="Delete task"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="tasks.links && tasks.links.length > 3" class="px-6 py-4 border-t border-gray-700">
          <div class="flex justify-center space-x-1">
            <Link
              v-for="link in tasks.links"
              :key="link.label"
              :href="link.url ?? undefined"
              :class="[
                'px-3 py-2 text-sm rounded-md transition-colors',
                link.active
                  ? 'bg-blue-600 text-white'
                  : link.url
                  ? 'text-gray-300 hover:bg-gray-700'
                  : 'text-gray-600 cursor-not-allowed'
              ]"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- Category Modal -->
    <div
      v-if="showCategoryModal"
      class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4 z-50"
      @click.self="showCategoryModal = false"
    >
      <div class="bg-gray-800 rounded-xl shadow-xl max-w-md w-full p-6 border border-gray-700">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold text-white">Add Category</h3>
          <button
            @click="showCategoryModal = false"
            class="text-gray-400 hover:text-gray-200 transition-colors"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </button>
        </div>

        <form @submit.prevent="submitCategory" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Category Name</label>
            <input
              v-model="categoryForm.name"
              type="text"
              placeholder="Enter category name"
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              :class="{ 'border-red-500': categoryForm.errors.name }"
            >
            <div v-if="categoryForm.errors.name" class="mt-1 text-sm text-red-400">
              {{ categoryForm.errors.name }}
            </div>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">Color</label>
            <input
              v-model="categoryForm.color"
              type="color"
              class="w-full h-10 bg-gray-700 border border-gray-600 rounded-lg"
            >
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showCategoryModal = false"
              class="px-4 py-2 text-gray-300 border border-gray-600 rounded-lg hover:bg-gray-700 transition-colors"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="categoryForm.processing"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 text-white rounded-lg transition-colors"
            >
              {{ categoryForm.processing ? 'Adding...' : 'Add Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Categories List -->
    <div v-if="categories.length > 0" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-8">
      <div class="bg-gray-800 rounded-xl shadow-lg border border-gray-700">
        <div class="p-6 border-b border-gray-700">
          <h2 class="text-xl font-semibold text-white">Categories</h2>
        </div>
        <div class="p-6">
          <div class="flex flex-wrap gap-3">
            <div
              v-for="category in categories"
              :key="category.id"
              class="flex items-center space-x-2 px-3 py-2 rounded-lg border border-gray-600 bg-gray-750"
            >
              <div
                class="w-4 h-4 rounded-full"
                :style="{ backgroundColor: category.color }"
              ></div>
              <span class="text-sm font-medium text-white">{{ category.name }}</span>
              <button
                @click="editCategory(category)"
                class="text-gray-400 hover:text-blue-400 transition-colors"
                title="Edit category"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
              </button>
              <button
                @click="deleteCategory(category)"
                class="text-gray-400 hover:text-red-400 transition-colors"
                title="Delete category"
              >
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                </svg>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useForm, router, Link } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

// -------------------------
// Types
// -------------------------
interface Category {
  id: number
  name: string
  color: string
}

interface Task {
  id: number
  title: string
  completed: boolean
  created_at: string
  category_id: number | null
  category?: Category
}

interface PaginationLinks {
  url: string | null
  label: string
  active: boolean
}

interface PaginatedTasks {
  data: Task[]
  total: number
  from?: number
  to?: number
  links: PaginationLinks[]
}

interface Filters {
  search?: string
  category?: string | number
  status?: string
}

interface Flash {
  success?: string
}

// -------------------------
// Props
// -------------------------
const props = defineProps<{
  tasks: PaginatedTasks
  categories: Category[]
  filters: Filters
  flash?: Flash
}>()

// -------------------------
// State
// -------------------------
const search = ref<string>(props.filters?.search || '')
const selectedCategory = ref<string | number>(props.filters?.category || '')
const selectedStatus = ref<string>(props.filters?.status || '')

let searchTimer: ReturnType<typeof setTimeout> | null = null

// -------------------------
// Watchers
// -------------------------
watch(search, () => {
  if (searchTimer) clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilters()
  }, 300)
})

// -------------------------
// Filtering
// -------------------------
const applyFilters = () => {
  router.get('/tasks', {
    search: search.value,
    category: selectedCategory.value,
    status: selectedStatus.value,
    page: 1
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['tasks', 'filters']
  })
}

const clearFilters = () => {
  search.value = ''
  selectedCategory.value = ''
  selectedStatus.value = ''
  applyFilters()
}

const hasActiveFilters = computed<boolean>(() => {
  return Boolean(search.value || selectedCategory.value || selectedStatus.value)
})

// -------------------------
// Task Form
// -------------------------
const form = useForm<{ title: string; category_id: number | null }>({
  title: '',
  category_id: null
})

const submit = () => {
  form.post('/tasks', {
    onSuccess: () => form.reset()
  })
}

// -------------------------
// Category Form
// -------------------------
const showCategoryModal = ref<boolean>(false)
const categoryForm = useForm<{ name: string; color: string }>({
  name: '',
  color: '#6366f1'
})

const submitCategory = () => {
  categoryForm.post('/categories', {
    onSuccess: () => {
      categoryForm.reset()
      showCategoryModal.value = false
    }
  })
}

const editCategory = (category: Category) => {
  const name = prompt('Edit category name:', category.name)
  if (name && name.trim()) {
    router.put(`/categories/${category.id}`, {
      name: name.trim(),
      color: category.color
    })
  }
}

const deleteCategory = (category: Category) => {
  if (confirm(`Delete category "${category.name}"? Tasks will be uncategorized.`)) {
    router.delete(`/categories/${category.id}`)
  }
}

// -------------------------
// Task Operations
// -------------------------
const editTask = (task: Task) => {
  const title = prompt('Edit task:', task.title)
  if (title && title.trim()) {
    router.put(`/tasks/${task.id}`, {
      title: title.trim(),
      category_id: task.category_id
    })
  }
}

const toggleTask = (task: Task) => {
  router.patch(`/tasks/${task.id}/toggle`)
}

const deleteTask = (task: Task) => {
  if (confirm(`Delete task: "${task.title}"?`)) {
    router.delete(`/tasks/${task.id}`)
  }
}

const updateTaskCategory = (task: Task, categoryId: string | number | null) => {
  router.put(`/tasks/${task.id}`, {
    title: task.title,
    category_id: categoryId ? Number(categoryId) : null
  })
}

// -------------------------
// Logout
// -------------------------
const logout = () => {
  if (confirm('Are you sure you want to logout?')) {
    router.post('/logout')
  }
}

// -------------------------
// Stats
// -------------------------
const stats = computed(() => {
  const allTasks = props.tasks.data || []
  return {
    pending: allTasks.filter(t => !t.completed).length,
    completed: allTasks.filter(t => t.completed).length,
    total: allTasks.length
  }
})
</script>