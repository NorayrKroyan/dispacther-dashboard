<template>
  <aside class="right-panel">
    <div class="panel-title">Dispatcher Job Journal</div>

    <div class="journal-stack">
      <article v-for="note in notes" :key="note.id" class="journal-card">
        <div class="journal-editor">
          <div class="note-toolbar">
            <div class="label">{{ note.is_editable ? 'Note Editor' : 'Journal Entry' }}</div>
            <div class="sub">{{ note.is_editable ? 'Editable' : 'Historical log' }}</div>
          </div>

          <div class="note-body">
            <textarea
              v-if="note.is_editable"
              class="note-textarea"
              :value="drafts[note.id] ?? note.note_text ?? ''"
              @input="drafts[note.id] = $event.target.value"
            ></textarea>

            <div v-else class="note-readonly">{{ note.note_text || '' }}</div>
          </div>
        </div>

        <div class="journal-meta">
          <template v-if="note.is_editable">
            <button class="meta-button" type="button" @click="saveExisting(note)">Save</button>
            <button class="meta-button" type="button" @click="clearExisting(note)">Clear</button>
          </template>
          <template v-else>
            <div class="meta-blank"></div>
            <div class="meta-blank"></div>
          </template>

          <div class="meta-label">Dispatcher</div>
          <div class="meta-value">{{ note.started_by_name || '-' }}</div>
          <div class="meta-value time">{{ formatDateTime(note.started_at) }}</div>
          <div class="meta-label">Shift</div>
          <div class="meta-value">{{ note.shift_key || '-' }}</div>
        </div>
      </article>

      <article v-if="!hasEditableNote" class="journal-card">
        <div class="journal-editor">
          <div class="note-toolbar">
            <div class="label">Note Editor</div>
            <div class="sub">New active note</div>
          </div>
          <div class="note-body">
            <textarea v-model="newNoteText" class="note-textarea"></textarea>
          </div>
        </div>

        <div class="journal-meta">
          <button class="meta-button" type="button" @click="saveNew">Save</button>
          <button class="meta-button" type="button" @click="clearNew">Clear</button>
          <div class="meta-label">Dispatcher</div>
          <div class="meta-value">{{ dispatcherName || '-' }}</div>
          <div class="meta-value time">{{ nowLabel }}</div>
          <div class="meta-label">Shift</div>
          <div class="meta-value">{{ shiftKey }}</div>
        </div>
      </article>
    </div>

    <div class="small-note">
      Only the top current shift note is editable by the dispatcher who started it. Notes below are historical log entries.
    </div>
  </aside>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'

const props = defineProps({
  notes: {
    type: Array,
    default: () => [],
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

const emit = defineEmits(['save-active'])

const drafts = reactive({})
const newNoteText = ref('')

watch(
  () => props.notes,
  (notes) => {
    Object.keys(drafts).forEach((key) => delete drafts[key])
    notes.forEach((note) => {
      drafts[note.id] = note.note_text ?? ''
    })
  },
  { immediate: true, deep: true },
)

const hasEditableNote = computed(() => props.notes.some((note) => note.is_editable))
const nowLabel = computed(() => new Date().toLocaleString())

function saveExisting(note) {
  emit('save-active', {
    shift_key: note.shift_key || props.shiftKey,
    started_by_name: note.started_by_name || props.dispatcherName,
    note_text: drafts[note.id] ?? '',
  })
}

function clearExisting(note) {
  drafts[note.id] = ''
  emit('save-active', {
    shift_key: note.shift_key || props.shiftKey,
    started_by_name: note.started_by_name || props.dispatcherName,
    note_text: '',
  })
}

function saveNew() {
  emit('save-active', {
    shift_key: props.shiftKey,
    started_by_name: props.dispatcherName,
    note_text: newNoteText.value,
  })
}

function clearNew() {
  newNoteText.value = ''
}

function formatDateTime(value) {
  if (!value) {
    return ''
  }

  const date = new Date(value)
  if (Number.isNaN(date.getTime())) {
    return value
  }

  return date.toLocaleString()
}
</script>
