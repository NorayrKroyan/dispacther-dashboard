<template>
  <div class="job-selector-bar">
    <div class="job-selector-left">
      <label class="field-label">Job</label>

      <select
        :value="selectedJoinId ?? ''"
        class="field-input job-field"
        @change="$emit('change-job', Number($event.target.value))"
      >
        <option disabled value="">Select job</option>
        <option v-for="job in jobs" :key="job.id_join" :value="job.id_join">
          {{ job.job_name }}
        </option>
      </select>

      <label class="field-label">Search</label>

      <input
        class="field-input search-field"
        :value="searchTerm"
        type="text"
        placeholder="Search personnel..."
        @input="$emit('update:search-term', $event.target.value)"
      />
    </div>

    <div class="job-selector-right">
      <label class="field-label">Dispatcher</label>

      <input
        class="field-input dispatcher-field"
        :value="dispatcherName"
        type="text"
        placeholder="Dispatcher name"
        @input="$emit('update:dispatcher-name', $event.target.value)"
      />

      <label class="field-label">Shift</label>

      <select
        :value="shiftKey"
        class="field-input small-field"
        @change="$emit('update:shift-key', $event.target.value)"
      >
        <option value="DAY">DAY</option>
        <option value="NIGHT">NIGHT</option>
        <option value="MID">MID</option>
      </select>
    </div>
  </div>
</template>

<script setup>
defineProps({
  jobs: {
    type: Array,
    default: () => [],
  },
  selectedJoinId: {
    type: Number,
    default: null,
  },
  searchTerm: {
    type: String,
    default: '',
  },
  dispatcherName: {
    type: String,
    default: '',
  },
  shiftKey: {
    type: String,
    default: 'DAY',
  },
})

defineEmits([
  'change-job',
  'update:search-term',
  'update:dispatcher-name',
  'update:shift-key',
])
</script>
