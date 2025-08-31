<script setup lang="js">

import { useForm, router, Link } from '@inertiajs/vue3'
import { ref, computed, watch } from 'vue'

const props = defineProps({ 
  tasks: Object, // Changed to Object to handle pagination
  categories: Array,
  filters: Object,
  flash: Object 
})

// Search and filter state
const search = ref(props.filters?.search || '')
const selectedCategory = ref(props.filters?.category || '')
const selectedStatus = ref(props.filters?.status || '')

// Debounce timer for search
let searchTimer = null

// Watch for search changes with debounce
watch(search, () => {
  clearTimeout(searchTimer)
  searchTimer = setTimeout(() => {
    applyFilters()
  }, 300)
})

// Apply filters
const applyFilters = () => {
  router.get('/tasks', {
    search: search.value,
    category: selectedCategory.value,
    status: selectedStatus.value,
    page: 1 // Reset to first page when filtering
  }, {
    preserveState: true,
    preserveScroll: true,
    only: ['tasks', 'filters']
  })
}

// New task form
const form = useForm({ 
  title: '',
  category_id: null
})

const submit = () => {
  form.post('/tasks', {
    onSuccess: () => form.reset()
  })
}

// Category management
const showCategoryModal = ref(false)
const categoryForm = useForm({
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

const editCategory = (category) => {
  const name = prompt("Edit category name:", category.name)
  if (name && name.trim()) {
    router.put(`/categories/${category.id}`, { 
      name: name.trim(),
      color: category.color
    })
  }
}

const deleteCategory = (category) => {
  if (confirm(`Delete category "${category.name}"? Tasks will be uncategorized.`)) {
    router.delete(`/categories/${category.id}`)
  }
}

// Edit task (updated to include category)
const editTask = (task) => {
  const title = prompt("Edit task:", task.title)
  if (title && title.trim()) {
    router.put(`/tasks/${task.id}`, { 
      title: title.trim(),
      category_id: task.category_id
    })
  }
}

// Toggle task completion
const toggleTask = (task) => {
  router.patch(`/tasks/${task.id}/toggle`)
}

// Delete task
const deleteTask = (task) => {
  if (confirm(`Delete task: "${task.title}"?`)) {
    router.delete(`/tasks/${task.id}`)
  }
}

// Update task category
const updateTaskCategory = (task, categoryId) => {
  router.put(`/tasks/${task.id}`, { 
    title: task.title,
    category_id: categoryId || null
  })
}

// Computed stats
const stats = computed(() => {
  const allTasks = props.tasks.data || []
  return {
    pending: allTasks.filter(t => !t.completed).length,
    completed: allTasks.filter(t => t.completed).length,
    total: allTasks.length
  }
})

// Clear all filters
const clearFilters = () => {
  search.value = ''
  selectedCategory.value = ''
  selectedStatus.value = ''
  applyFilters()
}

// Check if filters are active
const hasActiveFilters = computed(() => {
  return search.value || selectedCategory.value || selectedStatus.value
})
</script>

<template>
  <div class="min-h-screen bg-gray-900 py-8">
    <div class="max-w-4xl mx-auto px-4">
      <h1 class="text-3xl font-bold text-white mb-8 text-center">Task Manager</h1>
      
      <!-- Flash Messages -->
      <div v-if="flash?.success" class="mb-4 p-4 bg-green-900/50 border border-green-700 text-green-400 rounded">
        {{ flash.success }}
      </div>
      
      <!-- Search and Filters -->
      <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row gap-4">
          <!-- Search -->
          <div class="flex-1">
            <input 
              v-model="search"
              type="text"
              placeholder="Search tasks..." 
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            />
          </div>
          
          <!-- Category Filter -->
          <select 
            v-model="selectedCategory"
            @change="applyFilters"
            class="px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
          >
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          
          <!-- Status Filter -->
          <select 
            v-model="selectedStatus"
            @change="applyFilters"
            class="px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
          >
            <option value="">All Tasks</option>
            <option value="pending">Pending</option>
            <option value="completed">Completed</option>
          </select>
          
          <!-- Clear Filters -->
          <button 
            v-if="hasActiveFilters"
            @click="clearFilters"
            class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors"
          >
            Clear
          </button>
        </div>
      </div>
      
      <!-- Create New Task -->
      <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-6 mb-6">
        <div class="flex justify-between items-center mb-4">
          <h2 class="text-lg font-semibold text-gray-100">Add New Task</h2>
          <button 
            @click="showCategoryModal = true"
            class="text-sm px-3 py-1 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded transition-colors"
          >
            Manage Categories
          </button>
        </div>
        <form @submit.prevent="submit" class="flex gap-3">
          <input 
            v-model="form.title" 
            type="text"
            placeholder="Enter a new task..." 
            class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
            required
          />
          <select
            v-model="form.category_id"
            class="px-4 py-2 bg-gray-700 border border-gray-600 text-white rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
          >
            <option :value="null">No Category</option>
            <option v-for="category in categories" :key="category.id" :value="category.id">
              {{ category.name }}
            </option>
          </select>
          <button 
            type="submit" 
            :disabled="form.processing || !form.title.trim()"
            class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
          >
            {{ form.processing ? 'Adding...' : 'Add Task' }}
          </button>
        </form>
        <div v-if="form.errors.title" class="text-red-400 text-sm mt-2">
          {{ form.errors.title }}
        </div>
      </div>

      <!-- Task List -->
      <div class="bg-gray-800 rounded-lg shadow-xl border border-gray-700">
        <div class="p-6 border-b border-gray-700">
          <h2 class="text-lg font-semibold text-gray-100">
            Your Tasks 
            <span class="text-sm text-gray-400 font-normal">
              ({{ tasks.total || 0 }} total{{ hasActiveFilters ? ', filtered' : '' }})
            </span>
          </h2>
        </div>
        
        <div v-if="!tasks.data || tasks.data.length === 0" class="p-8 text-center text-gray-400">
          <div class="text-4xl mb-4">📝</div>
          <p class="text-lg">{{ hasActiveFilters ? 'No tasks found with current filters.' : 'No tasks yet!' }}</p>
          <p class="text-sm">{{ hasActiveFilters ? 'Try adjusting your filters.' : 'Add your first task above to get started.' }}</p>
        </div>
        
        <ul v-else class="divide-y divide-gray-700">
          <li 
            v-for="task in tasks.data" 
            :key="task.id" 
            class="p-4 hover:bg-gray-750 transition-colors"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center flex-1">
                <button
                  @click="toggleTask(task)"
                  class="mr-3 p-1 rounded-full hover:bg-gray-700 transition-colors"
                >
                  <div 
                    class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                    :class="task.completed ? 'bg-green-600 border-green-600 text-white' : 'border-gray-500 hover:border-green-500'"
                  >
                    <span v-if="task.completed" class="text-xs">✓</span>
                  </div>
                </button>
                <div class="flex-1">
                  <span 
                    class="transition-all"
                    :class="task.completed ? 'line-through text-gray-500' : 'text-gray-100'"
                  >
                    {{ task.title }}
                  </span>
                  <!-- Category Badge -->
                  <span 
                    v-if="task.category"
                    class="ml-2 px-2 py-1 text-xs rounded-full"
                    :style="`background-color: ${task.category.color}20; color: ${task.category.color}; border: 1px solid ${task.category.color}40;`"
                  >
                    {{ task.category.name }}
                  </span>
                </div>
                <span class="text-xs text-gray-500 ml-4">
                  {{ new Date(task.created_at).toLocaleDateString() }}
                </span>
              </div>
              
              <div class="flex items-center gap-2 ml-4">
                <!-- Category Selector -->
                <select
                  :value="task.category_id"
                  @change="updateTaskCategory(task, $event.target.value)"
                  class="p-1 text-xs bg-gray-700 border border-gray-600 text-gray-300 rounded"
                  title="Change category"
                >
                  <option :value="null">No Category</option>
                  <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                  </option>
                </select>
                
                <button
                  @click="editTask(task)"
                  class="p-2 text-gray-500 hover:text-yellow-400 hover:bg-gray-700 rounded transition-colors"
                  title="Edit task"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                </button>
                
                <button
                  @click="deleteTask(task)"
                  class="p-2 text-gray-500 hover:text-red-400 hover:bg-gray-700 rounded transition-colors"
                  title="Delete task"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                  </svg>
                </button>
              </div>
            </div>
          </li>
        </ul>
        
        <!-- Pagination -->
        <div v-if="tasks.links && tasks.links.length > 3" class="p-4 border-t border-gray-700">
          <div class="flex justify-center items-center gap-2">
            <Link
              v-for="link in tasks.links"
              :key="link.label"
              :href="link.url"
              :class="[
                'px-3 py-1 rounded transition-colors',
                link.active 
                  ? 'bg-indigo-600 text-white' 
                  : link.url 
                    ? 'bg-gray-700 text-gray-300 hover:bg-gray-600' 
                    : 'bg-gray-800 text-gray-600 cursor-not-allowed'
              ]"
              preserve-state
              preserve-scroll
              :only="['tasks']"
              v-html="link.label"
            />
          </div>
          <div class="text-center text-sm text-gray-500 mt-2">
            Showing {{ tasks.from || 0 }} to {{ tasks.to || 0 }} of {{ tasks.total || 0 }} tasks
          </div>
        </div>
      </div>

      <!-- Task Stats -->
      <div v-if="tasks.data && tasks.data.length > 0" class="mt-6 bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-4">
        <div class="flex justify-center gap-8 text-sm">
          <div class="text-center">
            <div class="text-2xl font-bold text-indigo-400">{{ stats.pending }}</div>
            <div class="text-gray-400">Pending</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-green-400">{{ stats.completed }}</div>
            <div class="text-gray-400">Completed</div>
          </div>
          <div class="text-center">
            <div class="text-2xl font-bold text-gray-300">{{ stats.total }}</div>
            <div class="text-gray-400">Total</div>
          </div>
        </div>
      </div>
      
      <!-- Categories Overview -->
      <div v-if="categories.length > 0" class="mt-6 bg-gray-800 rounded-lg shadow-xl border border-gray-700 p-4">
        <h3 class="text-lg font-semibold text-gray-100 mb-3">Categories</h3>
        <div class="flex flex-wrap gap-2">
          <div 
            v-for="category in categories" 
            :key="category.id"
            class="group flex items-center gap-2 px-3 py-1 rounded-full text-sm"
            :style="`background-color: ${category.color}20; color: ${category.color}; border: 1px solid ${category.color}40;`"
          >
            <span>{{ category.name }}</span>
            <button
              @click="editCategory(category)"
              class="opacity-0 group-hover:opacity-100 transition-opacity"
              title="Edit"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
              </svg>
            </button>
            <button
              @click="deleteCategory(category)"
              class="opacity-0 group-hover:opacity-100 transition-opacity"
              title="Delete"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
        </div>
      </div>
      
      <!-- Logout Button -->
      <div class="mt-8 text-center">
        <Link 
          href="/logout" 
          method="post" 
          as="button"
          class="px-6 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors"
        >
          Logout
        </Link>
      </div>
    </div>
    
    <!-- Category Modal -->
    <div v-if="showCategoryModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-gray-800 rounded-lg p-6 max-w-md w-full border border-gray-700">
        <h3 class="text-xl font-semibold text-white mb-4">Create New Category</h3>
        <form @submit.prevent="submitCategory">
          <div class="mb-4">
            <label class="block text-gray-300 text-sm mb-2">Category Name</label>
            <input 
              v-model="categoryForm.name"
              type="text"
              placeholder="e.g., Work, Personal, Urgent"
              class="w-full px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
              required
            />
          </div>
          <div class="mb-4">
            <label class="block text-gray-300 text-sm mb-2">Color</label>
            <div class="flex gap-2 items-center">
              <input 
                v-model="categoryForm.color"
                type="color"
                class="h-10 w-20 bg-gray-700 border border-gray-600 rounded cursor-pointer"
              />
              <input 
                v-model="categoryForm.color"
                type="text"
                pattern="^#[0-9A-Fa-f]{6}$"
                placeholder="#6366f1"
                class="flex-1 px-4 py-2 bg-gray-700 border border-gray-600 text-white placeholder-gray-400 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none"
                required
              />
            </div>
          </div>
          <div v-if="categoryForm.errors.name" class="text-red-400 text-sm mb-2">
            {{ categoryForm.errors.name }}
          </div>
          <div v-if="categoryForm.errors.color" class="text-red-400 text-sm mb-2">
            {{ categoryForm.errors.color }}
          </div>
          <div class="flex gap-3 justify-end">
            <button 
              type="button"
              @click="showCategoryModal = false"
              class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-gray-300 rounded-lg transition-colors"
            >
              Cancel
            </button>
            <button 
              type="submit"
              :disabled="categoryForm.processing || !categoryForm.name.trim()"
              class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
              {{ categoryForm.processing ? 'Creating...' : 'Create Category' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>