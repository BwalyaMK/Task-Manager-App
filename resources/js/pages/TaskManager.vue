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
  flash: Flash
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
