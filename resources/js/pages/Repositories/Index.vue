<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between items-center mb-8">
        <div>
          <h1 class="text-3xl font-bold text-gray-900">Git Repositories</h1>
          <p class="text-gray-600 mt-2">Automatically synced from GitHub</p>
        </div>
        <div class="text-sm text-gray-500 bg-white px-4 py-2 rounded-lg shadow-sm">
          Last synced: {{ formatDate(lastSynced) }}
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <div
          v-for="repo in repositories"
          :key="repo.id"
          class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 hover:shadow-md transition-all duration-200"
        >
          <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 truncate">{{ repo.name }}</h3>
            <span
              :class="[
                'px-3 py-1 text-xs font-medium rounded-full',
                repo.private ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800'
              ]"
            >
              {{ repo.private ? 'Private' : 'Public' }}
            </span>
          </div>

          <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ repo.description || 'No description' }}</p>

          <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
            <div class="flex items-center space-x-4">
              <span class="flex items-center">
                <GitBranch class="w-4 h-4 mr-1" />
                {{ repo.default_branch }}
              </span>
              <span class="flex items-center">
                <GitFork class="w-4 h-4 mr-1" />
                {{ repo.branches_count }}
              </span>
            </div>
          </div>

          <Link
            :href="route('repositories.branches', repo.id)"
            class="w-full inline-flex items-center justify-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors"
          >
            View Branches
            <ArrowRight class="w-4 h-4 ml-2" />
          </Link>
        </div>
      </div>

      <div v-if="repositories.length === 0" class="text-center py-12">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
          <GitBranch class="w-12 h-12 text-gray-400 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No repositories yet</h3>
          <p class="text-gray-600 mb-4">Repositories will appear here once they're created on GitHub</p>
          <a
            href="https://github.com/new"
            target="_blank"
            class="inline-flex items-center px-4 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors"
          >
            Create Repository on GitHub
            <ExternalLink class="w-4 h-4 ml-2" />
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { GitBranch, GitFork, ArrowRight, ExternalLink } from 'lucide-vue-next'

defineProps({
  repositories: {
    type: Array,
    default: () => []
  },
  lastSynced: {
    type: String,
    default: () => new Date().toISOString()
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
