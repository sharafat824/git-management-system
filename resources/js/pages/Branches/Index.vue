<template>
  <div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="mb-8">
        <Link
          :href="route('repositories.index')"
          class="inline-flex items-center text-gray-600 hover:text-gray-900 mb-4 transition-colors"
        >
          <ArrowLeft class="w-4 h-4 mr-2" />
          Back to repositories
        </Link>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
          <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ repository.name }}</h1>
          <p class="text-gray-600">{{ repository.full_name }}</p>
          <div class="flex items-center space-x-4 mt-4 text-sm text-gray-500">
            <span class="flex items-center">
              <GitBranch class="w-4 h-4 mr-1" />
              Default: {{ repository.default_branch }}
            </span>
            <span class="flex items-center">
              <GitFork class="w-4 h-4 mr-1" />
              {{ branches.length }} branches
            </span>
          </div>
        </div>
      </div>

      <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
          <h2 class="text-lg font-semibold text-gray-900">Branches</h2>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Branch Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created By
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Source
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Linked Task
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Created
                </th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
              <tr v-for="branch in branches" :key="branch.id" class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <GitBranch class="w-4 h-4 text-gray-400 mr-3" />
                    <span class="text-sm font-medium text-gray-900 font-mono">{{ branch.name }}</span>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ branch.created_by || 'N/A' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-500 font-mono">{{ branch.source_branch || 'N/A' }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    v-if="branch.linked_task"
                    class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ branch.linked_task }}
                  </span>
                  <span v-else class="text-sm text-gray-400">-</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ formatDate(branch.created_at) }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <div v-if="branches.length === 0" class="text-center py-12">
          <GitBranch class="w-12 h-12 text-gray-400 mx-auto mb-4" />
          <h3 class="text-lg font-medium text-gray-900 mb-2">No branches yet</h3>
          <p class="text-gray-600">Branches will appear here once they're created on GitHub</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
import { ArrowLeft, GitBranch, GitFork } from 'lucide-vue-next'

defineProps({
  repository: {
    type: Object,
    required: true
  },
  branches: {
    type: Array,
    default: () => []
  }
})

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('en-US', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}
</script>
