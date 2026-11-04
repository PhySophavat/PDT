import { createHotContext as __vite__createHotContext } from "/@vite/client";import.meta.hot = __vite__createHotContext("/resources/js/views/CourseSubjectView.vue");import { ref, computed, onMounted } from "/node_modules/.vite/deps/vue_dist_vue__esm-bundler__js.js?v=f4786049";
import { useI18n } from "/node_modules/.vite/deps/vue-i18n.js?v=f4786049";
import axios from "/node_modules/.vite/deps/axios.js?v=f4786049";
import PageHeader from "/resources/js/components/PageHeader.vue";
import TabNav from "/resources/js/components/TabNav.vue";
import EntityBanner from "/resources/js/components/EntityBanner.vue";
import StatCard from "/resources/js/components/StatCard.vue";
import FilterBar from "/resources/js/components/FilterBar.vue";
import DataTable from "/resources/js/components/DataTable.vue";


const _sfc_main = {
  __name: 'CourseSubjectView',
  setup(__props, { expose: __expose }) {
  __expose();

const { t } = useI18n();

const activeTab = ref('all');
const searchQuery = ref('');
const subjectsList = ref([]);

const showCreateModal = ref(false);
const showEditModal = ref(false);
const selectedSubject = ref(null);
const isSubmitting = ref(false);

const createForm = ref({
  code: '',
  name: '',
  subject_type: 'core',
  description: '',
  credit_hours: 3,
  grade_level: '7-12',
});

const editForm = ref({
  code: '',
  name: '',
  subject_type: 'core',
  description: '',
  credit_hours: 3,
  grade_level: '7-12',
});

const fetchSubjects = async () => {
  try {
    const res = await axios.get('/api/course-subjects');
    subjectsList.value = res.data.subjects || [];
  } catch (err) {
    console.error('Failed to fetch subjects:', err);
  }
};

onMounted(() => {
  fetchSubjects();
});

const filteredSubjects = computed(() => {
  return subjectsList.value.filter(s => {
    const matchesTab = activeTab.value === 'all' || s.type === activeTab.value || s.subject_type === activeTab.value;
    const q = searchQuery.value.toLowerCase();
    const matchesSearch = !q || 
      (s.nameKh && s.nameKh.toLowerCase().includes(q)) || 
      (s.name && s.name.toLowerCase().includes(q)) || 
      (s.nameEn && s.nameEn.toLowerCase().includes(q)) || 
      s.code.toLowerCase().includes(q);
    return matchesTab && matchesSearch;
  });
});

const totalCredits = computed(() => subjectsList.value.reduce((a, s) => a + (Number(s.credits || s.credit_hours) || 0), 0));

const openCreateModal = () => {
  createForm.value = {
    code: '',
    name: '',
    subject_type: 'core',
    description: '',
    credit_hours: 3,
    grade_level: '7-12',
  };
  showCreateModal.value = true;
};

const openEditModal = (s) => {
  selectedSubject.value = s;
  editForm.value = {
    code: s.code,
    name: s.nameKh || s.name,
    subject_type: s.subject_type || s.type || 'core',
    description: s.nameEn || s.description || '',
    credit_hours: s.credits || s.credit_hours || 3,
    grade_level: s.grade_level || '7-12',
  };
  showEditModal.value = true;
};

const submitCreateSubject = async () => {
  isSubmitting.value = true;
  try {
    const res = await axios.post('/api/course-subjects', createForm.value);
    showCreateModal.value = false;
    alert(res.data.message || '·??·?∂·??·??·??·??·??·?æ·?è·??·?ª·?Å·??·?∑·??·??·??·?∂·?ê·??·??·?∏·??·??·??·??·?ê·??!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || '·??·?∂·??·??·??·??·?†·?∂·??·??·??·?ª·??·??·?∂·??·??·??·??·??·?æ·?è·??·?ª·?Å·??·?∑·??·??·??·?∂!');
  } finally {
    isSubmitting.value = false;
  }
};

const submitUpdateSubject = async () => {
  if (!selectedSubject.value) return;
  isSubmitting.value = true;
  try {
    const res = await axios.put(`/api/course-subjects/${selectedSubject.value.id}`, editForm.value);
    showEditModal.value = false;
    alert(res.data.message || '·??·?∂·??·??·??·??·??·??·??·??·?ê·?è·??·??·?∂·??·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??·??·?ê·??!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || '·??·?∂·??·??·??·??·?†·?∂·??·??·??·?ª·??·??·?∂·??·??·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂!');
  } finally {
    isSubmitting.value = false;
  }
};

const handleDelete = async (s) => {
  if (!confirm(`·?è·?æ·?¢·??·??·??·??·?∑·?è·??·?∂·??·??·??·??·?ª·??·??·?ª·?Å·??·?∑·??·??·??·?∂ "${s.nameKh || s.name}" ·??·?Å·??·??·??·??·??·?Å?`)) return;
  try {
    const res = await axios.delete(`/api/course-subjects/${s.id}`);
    alert(res.data.message || '·??·?∂·??·??·?ª·??·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??·??·??·??·??·?ê·??!');
    await fetchSubjects();
  } catch (err) {
    alert(err.response?.data?.message || '·??·?∑·??·?¢·?∂·??·??·?ª·??·??·?ª·?Å·??·?∑·??·??·??·?∂·??·?Å·??·??·?∂·??·??·?Å!');
  }
};

const handlePrint = () => window.print();

const handleExport = () => {
  const csv = [
    ['·??·?º·??', '·??·??·??·??·??·?Å·??·??·??·??', '·??·??·??·??·??·?¢·??·??·??·??·??·?Å·??', '·??·??·??·??·?Å·??·??·?ª·?Å·??·?∑·??·??·??·?∂', '·??·??·??·??·??·?∑·??·??·??·?∂/·??·??·??·?è·?∂·?†·?ç', '·??·??·??·??·?∑·?è·?ê·??·??·?∂·??·??'].join(','),
    ...subjectsList.value.map(s => [
      `"${s.code}"`,
      `"${s.nameKh || s.name}"`,
      `"${s.nameEn || s.description || ''}"`,
      `"${s.typeLabel || s.type || ''}"`,
      `"${s.credits || s.credit_hours}"`,
      `"${s.grade_level || ''}"`
    ].join(','))
  ].join('\n');

  const blob = new Blob(["\uFEFF" + csv], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = `LMSTICK_Subjects_${new Date().toISOString().slice(0, 10)}.csv`;
  a.click();
};

const __returned__ = { t, activeTab, searchQuery, subjectsList, showCreateModal, showEditModal, selectedSubject, isSubmitting, createForm, editForm, fetchSubjects, filteredSubjects, totalCredits, openCreateModal, openEditModal, submitCreateSubject, submitUpdateSubject, handleDelete, handlePrint, handleExport, ref, computed, onMounted, get useI18n() { return useI18n }, get axios() { return axios }, PageHeader, TabNav, EntityBanner, StatCard, FilterBar, DataTable }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

}
import { createCommentVNode as _createCommentVNode, createVNode as _createVNode, createElementVNode as _createElementVNode, vModelText as _vModelText, withDirectives as _withDirectives, toDisplayString as _toDisplayString, createTextVNode as _createTextVNode, withCtx as _withCtx, openBlock as _openBlock, createElementBlock as _createElementBlock, renderList as _renderList, Fragment as _Fragment, normalizeClass as _normalizeClass, vModelSelect as _vModelSelect, withModifiers as _withModifiers, createStaticVNode as _createStaticVNode } from "/node_modules/.vite/deps/vue_dist_vue__esm-bundler__js.js?v=f4786049"

const _hoisted_1 = { class: "space-y-6" }
const _hoisted_2 = { class: "grid grid-cols-2 lg:grid-cols-4 gap-4" }
const _hoisted_3 = ["placeholder"]
const _hoisted_4 = { class: "text-xs text-slate-500" }
const _hoisted_5 = { class: "py-3 px-4 font-bold text-slate-700" }
const _hoisted_6 = { class: "py-3 px-4 font-bold text-slate-700" }
const _hoisted_7 = { class: "py-3 px-4 font-bold text-slate-700" }
const _hoisted_8 = { class: "py-3 px-4 font-bold text-slate-700 text-center" }
const _hoisted_9 = { class: "py-3 px-4 font-bold text-slate-700 text-center" }
const _hoisted_10 = { class: "py-3 px-4 font-bold text-slate-700 text-right" }
const _hoisted_11 = { key: 0 }
const _hoisted_12 = {
  colspan: "7",
  class: "py-10 text-center text-slate-400"
}
const _hoisted_13 = { class: "py-3 px-4 font-mono font-bold text-blue-600" }
const _hoisted_14 = { class: "py-3 px-4 font-bold text-slate-900" }
const _hoisted_15 = { class: "py-3 px-4 text-slate-600" }
const _hoisted_16 = { class: "py-3 px-4" }
const _hoisted_17 = { class: "py-3 px-4 text-center font-semibold text-slate-700" }
const _hoisted_18 = { class: "py-3 px-4 text-center" }
const _hoisted_19 = { class: "px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100" }
const _hoisted_20 = { class: "py-3 px-4 text-right space-x-1.5 whitespace-nowrap" }
const _hoisted_21 = ["onClick"]
const _hoisted_22 = ["onClick"]
const _hoisted_23 = {
  key: 0,
  class: "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
}
const _hoisted_24 = { class: "bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150" }
const _hoisted_25 = { class: "px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50" }
const _hoisted_26 = { class: "grid grid-cols-2 gap-4" }
const _hoisted_27 = { class: "grid grid-cols-2 gap-4" }
const _hoisted_28 = { class: "flex items-center justify-end gap-2 pt-3 border-t border-slate-100" }
const _hoisted_29 = ["disabled"]
const _hoisted_30 = { key: 0 }
const _hoisted_31 = { key: 1 }
const _hoisted_32 = {
  key: 1,
  class: "fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/50 backdrop-blur-sm"
}
const _hoisted_33 = { class: "bg-white rounded-2xl w-full max-w-lg shadow-2xl border border-slate-100 overflow-hidden animate-in fade-in zoom-in-95 duration-150" }
const _hoisted_34 = { class: "px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50" }
const _hoisted_35 = { class: "font-bold text-slate-800 text-base flex items-center gap-2" }
const _hoisted_36 = { class: "grid grid-cols-2 gap-4" }
const _hoisted_37 = { class: "grid grid-cols-2 gap-4" }
const _hoisted_38 = { class: "flex items-center justify-end gap-2 pt-3 border-t border-slate-100" }
const _hoisted_39 = ["disabled"]
const _hoisted_40 = { key: 0 }
const _hoisted_41 = { key: 1 }

function _sfc_render(_ctx, _cache, $props, $setup, $data, $options) {
  return (_openBlock(), _createElementBlock("div", _hoisted_1, [
    _createCommentVNode(" Header "),
    _createVNode($setup["PageHeader"], {
      title: _ctx.$t('subjects.title'),
      breadcrumb: _ctx.$t('nav.inventory'),
      actionLabel: _ctx.$t('subjects.addSubject'),
      onAction: $setup.openCreateModal
    }, null, 8 /* PROPS */, ["title", "breadcrumb", "actionLabel"]),
    _createCommentVNode(" Sub Tabs "),
    _createVNode($setup["TabNav"], {
      modelValue: $setup.activeTab,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => (($setup.activeTab) = $event)),
      tabs: [
        { id: 'all', label: `·??·?∂·??·??·?¢·??·?? (${$setup.subjectsList.length})` },
        { id: 'core', label: '‚≠ê ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??' },
        { id: 'science', label: '??¨ ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·?∑·?è' },
        { id: 'social', label: '??ç ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·??·??·??·??' },
        { id: 'languages', label: '??£Ô∏è ·??·?∂·??·?∂' },
        { id: 'elective', label: '??® ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??·?æ·??·??·?æ·??' }
      ]
    }, null, 8 /* PROPS */, ["modelValue", "tabs"]),
    _createCommentVNode(" Entity Banner "),
    _createVNode($setup["EntityBanner"], {
      name: _ctx.$t('subjects.title'),
      subtitle: _ctx.$t('nav.inventory') + ' - ·??·?∂·?è·?∂·?°·?ª·??·??·?ª·?Å·??·?∑·??·??·??·?∂ ·??·??·??·??·??·?∑·??·?∏·??·?∑·??·??·??·?∂ ·??·?∑·??·??·??·??·??·?ª·??·??·??·??·??·??·??·??·??·??·??',
      metaId: "ADM-SBJ-CAT",
      onPrint: $setup.handlePrint,
      onDownload: $setup.handleExport
    }, null, 8 /* PROPS */, ["name", "subtitle"]),
    _createCommentVNode(" 4 Statistics Summary Cards "),
    _createElementVNode("div", _hoisted_2, [
      _createVNode($setup["StatCard"], {
        title: _ctx.$t('subjects.totalSubjects'),
        value: $setup.subjectsList.length
      }, null, 8 /* PROPS */, ["title", "value"]),
      _createVNode($setup["StatCard"], {
        title: _ctx.$t('subjects.totalCredits'),
        value: $setup.totalCredits
      }, null, 8 /* PROPS */, ["title", "value"]),
      _createVNode($setup["StatCard"], {
        title: _ctx.$t('classes.gradeLevel'),
        value: '·?ß ·??·??·?? ·?°·?¢'
      }, null, 8 /* PROPS */, ["title"]),
      _createVNode($setup["StatCard"], {
        title: _ctx.$t('common.status'),
        value: _ctx.$t('common.active')
      }, null, 8 /* PROPS */, ["title", "value"])
    ]),
    _createCommentVNode(" Filter Bar "),
    _createVNode($setup["FilterBar"], null, {
      left: _withCtx(() => [
        _withDirectives(_createElementVNode("input", {
          "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => (($setup.searchQuery) = $event)),
          type: "text",
          placeholder: `${_ctx.$t('common.search')} ${_ctx.$t('subjects.title')} (·??·??·??·??·??, ·??·?º·??)...`,
          class: "px-3.5 py-1.5 bg-white border border-slate-200 rounded-xl text-xs w-64 focus:outline-none focus:ring-2 focus:ring-blue-500/20"
        }, null, 8 /* PROPS */, _hoisted_3), [
          [_vModelText, $setup.searchQuery]
        ])
      ]),
      right: _withCtx(() => [
        _createElementVNode("span", _hoisted_4, [
          _createTextVNode(_toDisplayString(_ctx.$t('subjects.totalSubjects')) + ": ", 1 /* TEXT */),
          _createElementVNode("strong", null, _toDisplayString($setup.filteredSubjects.length), 1 /* TEXT */)
        ])
      ]),
      _: 1 /* STABLE */
    }),
    _createCommentVNode(" Data Table "),
    _createVNode($setup["DataTable"], null, {
      header: _withCtx(() => [
        _createElementVNode("tr", null, [
          _createElementVNode("th", _hoisted_5, _toDisplayString(_ctx.$t('subjects.subjectCode')), 1 /* TEXT */),
          _createElementVNode("th", _hoisted_6, _toDisplayString(_ctx.$t('subjects.khmerTitle')), 1 /* TEXT */),
          _createElementVNode("th", _hoisted_7, _toDisplayString(_ctx.$t('subjects.englishTitle')), 1 /* TEXT */),
          _cache[18] || (_cache[18] = _createElementVNode("th", { class: "py-3 px-4 font-bold text-slate-700" }, "·??·??·??·??·?Å·??·??·?ª·?Å·??·?∑·??·??·??·?∂", -1 /* CACHED */)),
          _createElementVNode("th", _hoisted_8, _toDisplayString(_ctx.$t('subjects.credits')), 1 /* TEXT */),
          _createElementVNode("th", _hoisted_9, _toDisplayString(_ctx.$t('common.status')), 1 /* TEXT */),
          _createElementVNode("th", _hoisted_10, _toDisplayString(_ctx.$t('common.actions')), 1 /* TEXT */)
        ])
      ]),
      body: _withCtx(() => [
        ($setup.filteredSubjects.length === 0)
          ? (_openBlock(), _createElementBlock("tr", _hoisted_11, [
              _createElementVNode("td", _hoisted_12, _toDisplayString(_ctx.$t('common.noData')), 1 /* TEXT */)
            ]))
          : _createCommentVNode("v-if", true),
        (_openBlock(true), _createElementBlock(_Fragment, null, _renderList($setup.filteredSubjects, (s) => {
          return (_openBlock(), _createElementBlock("tr", {
            key: s.id || s.code,
            class: "hover:bg-slate-50/70 transition-colors"
          }, [
            _createElementVNode("td", _hoisted_13, _toDisplayString(s.code), 1 /* TEXT */),
            _createElementVNode("td", _hoisted_14, _toDisplayString(s.nameKh || s.name), 1 /* TEXT */),
            _createElementVNode("td", _hoisted_15, _toDisplayString(s.nameEn || s.description || '-'), 1 /* TEXT */),
            _createElementVNode("td", _hoisted_16, [
              _createElementVNode("span", {
                class: _normalizeClass([
              'px-2.5 py-1 rounded-lg text-[11px] font-bold inline-flex items-center gap-1.5',
              s.type === 'science' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
              s.type === 'languages' ? 'bg-purple-50 text-purple-700 border border-purple-200' :
              s.type === 'social' ? 'bg-amber-50 text-amber-700 border border-amber-200' :
              s.type === 'core' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' :
              s.type === 'elective' ? 'bg-teal-50 text-teal-700 border border-teal-200' :
              'bg-slate-100 text-slate-700 border border-slate-200'
            ])
              }, [
                _createElementVNode("span", {
                  class: _normalizeClass(["w-1.5 h-1.5 rounded-full", [
                s.type === 'science' ? 'bg-blue-500' :
                s.type === 'languages' ? 'bg-purple-500' :
                s.type === 'social' ? 'bg-amber-500' :
                s.type === 'core' ? 'bg-emerald-500' :
                s.type === 'elective' ? 'bg-teal-500' :
                'bg-slate-400'
              ]])
                }, null, 2 /* CLASS */),
                _createTextVNode(" " + _toDisplayString(s.typeLabel || '·??·?º·??·??'), 1 /* TEXT */)
              ], 2 /* CLASS */)
            ]),
            _createElementVNode("td", _hoisted_17, _toDisplayString(s.credits || s.credit_hours) + " ·??·??·??·??/·??·??·??·?è·?∂·?†·?ç", 1 /* TEXT */),
            _createElementVNode("td", _hoisted_18, [
              _createElementVNode("span", _hoisted_19, _toDisplayString(_ctx.$t('common.active')), 1 /* TEXT */)
            ]),
            _createElementVNode("td", _hoisted_20, [
              _createElementVNode("button", {
                onClick: $event => ($setup.openEditModal(s)),
                class: "px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-xs font-semibold hover:bg-blue-100 transition"
              }, _toDisplayString(_ctx.$t('common.edit')), 9 /* TEXT, PROPS */, _hoisted_21),
              _createElementVNode("button", {
                onClick: $event => ($setup.handleDelete(s)),
                class: "px-2 py-1 bg-rose-50 text-rose-600 rounded-lg text-xs font-semibold hover:bg-rose-100 transition"
              }, _toDisplayString(_ctx.$t('common.delete')), 9 /* TEXT, PROPS */, _hoisted_22)
            ])
          ]))
        }), 128 /* KEYED_FRAGMENT */))
      ]),
      _: 1 /* STABLE */
    }),
    _createCommentVNode(" Modal 1: Create Subject Modal "),
    ($setup.showCreateModal)
      ? (_openBlock(), _createElementBlock("div", _hoisted_23, [
          _createElementVNode("div", _hoisted_24, [
            _createElementVNode("div", _hoisted_25, [
              _cache[19] || (_cache[19] = _createElementVNode("h3", { class: "font-bold text-slate-800 text-base flex items-center gap-2" }, [
                _createElementVNode("span", { class: "w-2.5 h-2.5 rounded-full bg-blue-600" }),
                _createTextVNode(" + ·??·??·??·?ê·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂·?ê·??·??·?∏ ")
              ], -1 /* CACHED */)),
              _createElementVNode("button", {
                onClick: _cache[2] || (_cache[2] = $event => ($setup.showCreateModal = false)),
                class: "text-slate-400 hover:text-slate-600 text-lg"
              }, "√?")
            ]),
            _createElementVNode("form", {
              onSubmit: _withModifiers($setup.submitCreateSubject, ["prevent"]),
              class: "p-6 space-y-4"
            }, [
              _createElementVNode("div", _hoisted_26, [
                _createElementVNode("div", null, [
                  _cache[20] || (_cache[20] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·?º·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (Code) *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("input", {
                    "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => (($setup.createForm.code) = $event)),
                    type: "text",
                    required: "",
                    placeholder: "·?ß. MTH-01",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 uppercase"
                  }, null, 512 /* NEED_PATCH */), [
                    [_vModelText, $setup.createForm.code]
                  ])
                ]),
                _createElementVNode("div", null, [
                  _cache[21] || (_cache[21] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·?∑·??·??·??·?∂/·??·??·??·?è·?∂·?†·?ç *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("input", {
                    "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => (($setup.createForm.credit_hours) = $event)),
                    type: "number",
                    min: "1",
                    max: "15",
                    required: "",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, null, 512 /* NEED_PATCH */), [
                    [
                      _vModelText,
                      $setup.createForm.credit_hours,
                      void 0,
                      { number: true }
                    ]
                  ])
                ])
              ]),
              _createElementVNode("div", null, [
                _cache[22] || (_cache[22] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (·??·?∂·??·?∂·?Å·??·??·??·??) *", -1 /* CACHED */)),
                _withDirectives(_createElementVNode("input", {
                  "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => (($setup.createForm.name) = $event)),
                  type: "text",
                  required: "",
                  placeholder: "·?ß. ·??·??·?∑·?è·??·?∑·??·??·??·?∂",
                  class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                }, null, 512 /* NEED_PATCH */), [
                  [_vModelText, $setup.createForm.name]
                ])
              ]),
              _createElementVNode("div", null, [
                _cache[23] || (_cache[23] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (·?¢·??·??·??·??·??·?Å·?? / ·??·?∂·??·??·?∑·??·??·??·??·?∂)", -1 /* CACHED */)),
                _withDirectives(_createElementVNode("input", {
                  "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => (($setup.createForm.description) = $event)),
                  type: "text",
                  placeholder: "·?ß. Mathematics",
                  class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                }, null, 512 /* NEED_PATCH */), [
                  [_vModelText, $setup.createForm.description]
                ])
              ]),
              _createElementVNode("div", _hoisted_27, [
                _createElementVNode("div", null, [
                  _cache[25] || (_cache[25] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·?Å·??·??·?ª·?Å·??·?∑·??·??·??·?∂ *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("select", {
                    "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => (($setup.createForm.subject_type) = $event)),
                    required: "",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, [...(_cache[24] || (_cache[24] = [
                    _createStaticVNode("<option value=\"core\">‚≠ê ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·?? / ·??·??·??·?º·??</option><option value=\"science\">??¨ ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·?∑·?è (Science)</option><option value=\"social\">??ç ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·??·??·??·?? (Social Science)</option><option value=\"languages\">??£Ô∏è ·??·?∂·??·?∂·??·??·??·?Å·?? / ·??·?∂·??·?∂</option><option value=\"elective\">??® ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??·?æ·??·??·?æ·?? / ·??·??·??·?∑·??</option><option value=\"general\">??? ·??·?º·??·?? (General)</option>", 6)
                  ]))], 512 /* NEED_PATCH */), [
                    [_vModelSelect, $setup.createForm.subject_type]
                  ])
                ]),
                _createElementVNode("div", null, [
                  _cache[27] || (_cache[27] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·?∑·?è·?ê·??·??·?∂·??·??·??·??·??·?è·??·??·?º·??·??·??·??", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("select", {
                    "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => (($setup.createForm.grade_level) = $event)),
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, [...(_cache[26] || (_cache[26] = [
                    _createElementVNode("option", { value: "7-12" }, "·?ê·??·??·?∂·??·??·??·?∏ ·?ß ·??·??·?? ·?°·?¢ (·??·?º·??·??)", -1 /* CACHED */),
                    _createElementVNode("option", { value: "7-9" }, "·?¢·??·?ª·??·?∑·??·??·??·?∂·??·?ê·?? (·?ß - ·?©)", -1 /* CACHED */),
                    _createElementVNode("option", { value: "10-12" }, "·??·?∑·??·??·??·?∂·??·?ê·?? (·?°·?† - ·?°·?¢)", -1 /* CACHED */)
                  ]))], 512 /* NEED_PATCH */), [
                    [_vModelSelect, $setup.createForm.grade_level]
                  ])
                ])
              ]),
              _createElementVNode("div", _hoisted_28, [
                _createElementVNode("button", {
                  type: "button",
                  onClick: _cache[9] || (_cache[9] = $event => ($setup.showCreateModal = false)),
                  class: "px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200"
                }, " ·??·??·??·??·??·?? "),
                _createElementVNode("button", {
                  type: "submit",
                  disabled: $setup.isSubmitting,
                  class: "px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50"
                }, [
                  ($setup.isSubmitting)
                    ? (_openBlock(), _createElementBlock("span", _hoisted_30, "·??·??·??·?ª·??·??·??·??·??·?∂·??·?ª·??..."))
                    : (_openBlock(), _createElementBlock("span", _hoisted_31, "·??·??·??·??·?∂·??·?ª·??·??·?ª·?Å·??·?∑·??·??·??·?∂"))
                ], 8 /* PROPS */, _hoisted_29)
              ])
            ], 32 /* NEED_HYDRATION */)
          ])
        ]))
      : _createCommentVNode("v-if", true),
    _createCommentVNode(" Modal 2: Edit Subject Modal "),
    ($setup.showEditModal)
      ? (_openBlock(), _createElementBlock("div", _hoisted_32, [
          _createElementVNode("div", _hoisted_33, [
            _createElementVNode("div", _hoisted_34, [
              _createElementVNode("h3", _hoisted_35, [
                _cache[28] || (_cache[28] = _createElementVNode("span", { class: "w-2.5 h-2.5 rounded-full bg-amber-500" }, null, -1 /* CACHED */)),
                _createTextVNode(" ·??·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂ " + _toDisplayString($setup.selectedSubject?.name), 1 /* TEXT */)
              ]),
              _createElementVNode("button", {
                onClick: _cache[10] || (_cache[10] = $event => ($setup.showEditModal = false)),
                class: "text-slate-400 hover:text-slate-600 text-lg"
              }, "√?")
            ]),
            _createElementVNode("form", {
              onSubmit: _withModifiers($setup.submitUpdateSubject, ["prevent"]),
              class: "p-6 space-y-4"
            }, [
              _createElementVNode("div", _hoisted_36, [
                _createElementVNode("div", null, [
                  _cache[29] || (_cache[29] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·?º·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (Code) *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("input", {
                    "onUpdate:modelValue": _cache[11] || (_cache[11] = $event => (($setup.editForm.code) = $event)),
                    type: "text",
                    required: "",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 uppercase"
                  }, null, 512 /* NEED_PATCH */), [
                    [_vModelText, $setup.editForm.code]
                  ])
                ]),
                _createElementVNode("div", null, [
                  _cache[30] || (_cache[30] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·?∑·??·??·??·?∂/·??·??·??·?è·?∂·?†·?ç *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("input", {
                    "onUpdate:modelValue": _cache[12] || (_cache[12] = $event => (($setup.editForm.credit_hours) = $event)),
                    type: "number",
                    min: "1",
                    max: "15",
                    required: "",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, null, 512 /* NEED_PATCH */), [
                    [
                      _vModelText,
                      $setup.editForm.credit_hours,
                      void 0,
                      { number: true }
                    ]
                  ])
                ])
              ]),
              _createElementVNode("div", null, [
                _cache[31] || (_cache[31] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (·??·?∂·??·?∂·?Å·??·??·??·??) *", -1 /* CACHED */)),
                _withDirectives(_createElementVNode("input", {
                  "onUpdate:modelValue": _cache[13] || (_cache[13] = $event => (($setup.editForm.name) = $event)),
                  type: "text",
                  required: "",
                  class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                }, null, 512 /* NEED_PATCH */), [
                  [_vModelText, $setup.editForm.name]
                ])
              ]),
              _createElementVNode("div", null, [
                _cache[32] || (_cache[32] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·??·??·?ª·?Å·??·?∑·??·??·??·?∂ (·?¢·??·??·??·??·??·?Å·?? / ·??·?∂·??·??·?∑·??·??·??·??·?∂)", -1 /* CACHED */)),
                _withDirectives(_createElementVNode("input", {
                  "onUpdate:modelValue": _cache[14] || (_cache[14] = $event => (($setup.editForm.description) = $event)),
                  type: "text",
                  class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                }, null, 512 /* NEED_PATCH */), [
                  [_vModelText, $setup.editForm.description]
                ])
              ]),
              _createElementVNode("div", _hoisted_37, [
                _createElementVNode("div", null, [
                  _cache[34] || (_cache[34] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·?Å·??·??·?ª·?Å·??·?∑·??·??·??·?∂ *", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("select", {
                    "onUpdate:modelValue": _cache[15] || (_cache[15] = $event => (($setup.editForm.subject_type) = $event)),
                    required: "",
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, [...(_cache[33] || (_cache[33] = [
                    _createStaticVNode("<option value=\"core\">‚≠ê ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·?? / ·??·??·??·?º·??</option><option value=\"science\">??¨ ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·?∑·?è (Science)</option><option value=\"social\">??ç ·??·?∑·??·??·??·?∂·??·?∂·??·??·?è·??·??·??·??·??·??·?? (Social Science)</option><option value=\"languages\">??£Ô∏è ·??·?∂·??·?∂·??·??·??·?Å·?? / ·??·?∂·??·?∂</option><option value=\"elective\">??® ·??·?ª·?Å·??·?∑·??·??·??·?∂·??·??·??·?æ·??·??·?æ·?? / ·??·??·??·?∑·??</option><option value=\"general\">??? ·??·?º·??·?? (General)</option>", 6)
                  ]))], 512 /* NEED_PATCH */), [
                    [_vModelSelect, $setup.editForm.subject_type]
                  ])
                ]),
                _createElementVNode("div", null, [
                  _cache[36] || (_cache[36] = _createElementVNode("label", { class: "block text-xs font-bold text-slate-700 mb-1" }, "·??·??·??·??·?∑·?è·?ê·??·??·?∂·??·??·??·??·??·?è·??·??·?º·??·??·??·??", -1 /* CACHED */)),
                  _withDirectives(_createElementVNode("select", {
                    "onUpdate:modelValue": _cache[16] || (_cache[16] = $event => (($setup.editForm.grade_level) = $event)),
                    class: "w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20"
                  }, [...(_cache[35] || (_cache[35] = [
                    _createElementVNode("option", { value: "7-12" }, "·?ê·??·??·?∂·??·??·??·?∏ ·?ß ·??·??·?? ·?°·?¢ (·??·?º·??·??)", -1 /* CACHED */),
                    _createElementVNode("option", { value: "7-9" }, "·?¢·??·?ª·??·?∑·??·??·??·?∂·??·?ê·?? (·?ß - ·?©)", -1 /* CACHED */),
                    _createElementVNode("option", { value: "10-12" }, "·??·?∑·??·??·??·?∂·??·?ê·?? (·?°·?† - ·?°·?¢)", -1 /* CACHED */)
                  ]))], 512 /* NEED_PATCH */), [
                    [_vModelSelect, $setup.editForm.grade_level]
                  ])
                ])
              ]),
              _createElementVNode("div", _hoisted_38, [
                _createElementVNode("button", {
                  type: "button",
                  onClick: _cache[17] || (_cache[17] = $event => ($setup.showEditModal = false)),
                  class: "px-4 py-2 bg-slate-100 text-slate-600 rounded-xl text-xs font-semibold hover:bg-slate-200"
                }, " ·??·??·??·??·??·?? "),
                _createElementVNode("button", {
                  type: "submit",
                  disabled: $setup.isSubmitting,
                  class: "px-5 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 transition disabled:opacity-50"
                }, [
                  ($setup.isSubmitting)
                    ? (_openBlock(), _createElementBlock("span", _hoisted_40, "·??·??·??·?ª·??·??·??·??·??·?∂·??·?ª·??..."))
                    : (_openBlock(), _createElementBlock("span", _hoisted_41, "·??·??·??·??·?∂·??·?ª·??·??·?∂·??·??·??·??·??·??·??"))
                ], 8 /* PROPS */, _hoisted_39)
              ])
            ], 32 /* NEED_HYDRATION */)
          ])
        ]))
      : _createCommentVNode("v-if", true)
  ]))
}



_sfc_main.__hmrId = "0a6cdb0a"
typeof __VUE_HMR_RUNTIME__ !== 'undefined' && __VUE_HMR_RUNTIME__.createRecord(_sfc_main.__hmrId, _sfc_main)
import.meta.hot.on('file-changed', ({ file }) => {
  __VUE_HMR_RUNTIME__.CHANGED_FILE = file
})
import.meta.hot.accept(mod => {
  if (!mod) return
  const { default: updated, _rerender_only } = mod
  if (_rerender_only) {
    __VUE_HMR_RUNTIME__.rerender(updated.__hmrId, updated.render)
  } else {
    __VUE_HMR_RUNTIME__.reload(updated.__hmrId, updated)
  }
})
import _export_sfc from "/@id/__x00__plugin-vue:export-helper"
export default /*#__PURE__*/_export_sfc(_sfc_main, [['render',_sfc_render],['__file',"D:/project/PDT/PDT/resources/js/views/CourseSubjectView.vue"]])
//# sourceMappingURL=data:application/json;base64,eyJ2ZXJzaW9uIjozLCJuYW1lcyI6W10sInNvdXJjZXMiOlsiQ291cnNlU3ViamVjdFZpZXcudnVlIl0sInNvdXJjZXNDb250ZW50IjpbIjx0ZW1wbGF0ZT5cbiAgPGRpdiBjbGFzcz1cInNwYWNlLXktNlwiPlxuICAgIDwhLS0gSGVhZGVyIC0tPlxuICAgIDxQYWdlSGVhZGVyIFxuICAgICAgOnRpdGxlPVwiJHQoJ3N1YmplY3RzLnRpdGxlJylcIiBcbiAgICAgIDpicmVhZGNydW1iPVwiJHQoJ25hdi5pbnZlbnRvcnknKVwiIFxuICAgICAgOmFjdGlvbkxhYmVsPVwiJHQoJ3N1YmplY3RzLmFkZFN1YmplY3QnKVwiXG4gICAgICBAYWN0aW9uPVwib3BlbkNyZWF0ZU1vZGFsXCJcbiAgICAvPlxuXG4gICAgPCEtLSBTdWIgVGFicyAtLT5cbiAgICA8VGFiTmF2IFxuICAgICAgdi1tb2RlbD1cImFjdGl2ZVRhYlwiIFxuICAgICAgOnRhYnM9XCJbXG4gICAgICAgIHsgaWQ6ICdhbGwnLCBsYWJlbDogYOGekeGetuGfhuGehOGeouGen+GfiyAoJHtzdWJqZWN0c0xpc3QubGVuZ3RofSlgIH0sXG4gICAgICAgIHsgaWQ6ICdjb3JlJywgbGFiZWw6ICfirZAg4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z624Z6C4Z+E4Z6bJyB9LFxuICAgICAgICB7IGlkOiAnc2NpZW5jZScsIGxhYmVsOiAn8J+UrCDhnpzhnrfhnpHhn5Lhnpnhnrbhnp/hnrbhnp/hn5Lhno/hn5Lhnprhnpbhnrfhno8nIH0sXG4gICAgICAgIHsgaWQ6ICdzb2NpYWwnLCBsYWJlbDogJ/CfjI0g4Z6c4Z634Z6R4Z+S4Z6Z4Z624Z6f4Z624Z6f4Z+S4Z6P4Z+S4Z6a4Z6f4Z6E4Z+S4Z6C4Z6YJyB9LFxuICAgICAgICB7IGlkOiAnbGFuZ3VhZ2VzJywgbGFiZWw6ICfwn5ej77iPIOGel+GetuGen+GeticgfSxcbiAgICAgICAgeyBpZDogJ2VsZWN0aXZlJywgbGFiZWw6ICfwn46oIOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGeh+GfkuGemuGevuGen+GemuGevuGenycgfVxuICAgICAgXVwiXG4gICAgLz5cblxuICAgIDwhLS0gRW50aXR5IEJhbm5lciAtLT5cbiAgICA8RW50aXR5QmFubmVyIFxuICAgICAgOm5hbWU9XCIkdCgnc3ViamVjdHMudGl0bGUnKVwiXG4gICAgICA6c3VidGl0bGU9XCIkdCgnbmF2LmludmVudG9yeScpICsgJyAtIOGegOGetuGej+GetuGeoeGeu+GegOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiDhnoDhnpjhn5LhnpjhnpzhnrfhnpLhnrjhnp/hnrfhnoDhn5Lhnp/hnrYg4Z6T4Z634Z6E4Z6U4Z6T4Z+S4Z6R4Z674Z6A4Z6Y4Z+J4Z+E4Z6E4Z6U4Z6E4Z+S4Z6a4Z+A4Z6TJ1wiXG4gICAgICBtZXRhSWQ9XCJBRE0tU0JKLUNBVFwiXG4gICAgICBAcHJpbnQ9XCJoYW5kbGVQcmludFwiXG4gICAgICBAZG93bmxvYWQ9XCJoYW5kbGVFeHBvcnRcIlxuICAgIC8+XG5cbiAgICA8IS0tIDQgU3RhdGlzdGljcyBTdW1tYXJ5IENhcmRzIC0tPlxuICAgIDxkaXYgY2xhc3M9XCJncmlkIGdyaWQtY29scy0yIGxnOmdyaWQtY29scy00IGdhcC00XCI+XG4gICAgICA8U3RhdENhcmQgOnRpdGxlPVwiJHQoJ3N1YmplY3RzLnRvdGFsU3ViamVjdHMnKVwiIDp2YWx1ZT1cInN1YmplY3RzTGlzdC5sZW5ndGhcIiAvPlxuICAgICAgPFN0YXRDYXJkIDp0aXRsZT1cIiR0KCdzdWJqZWN0cy50b3RhbENyZWRpdHMnKVwiIDp2YWx1ZT1cInRvdGFsQ3JlZGl0c1wiIC8+XG4gICAgICA8U3RhdENhcmQgOnRpdGxlPVwiJHQoJ2NsYXNzZXMuZ3JhZGVMZXZlbCcpXCIgOnZhbHVlPVwiJ+GfpyDhnorhnpvhn4sg4Z+h4Z+iJ1wiIC8+XG4gICAgICA8U3RhdENhcmQgOnRpdGxlPVwiJHQoJ2NvbW1vbi5zdGF0dXMnKVwiIDp2YWx1ZT1cIiR0KCdjb21tb24uYWN0aXZlJylcIiAvPlxuICAgIDwvZGl2PlxuXG4gICAgPCEtLSBGaWx0ZXIgQmFyIC0tPlxuICAgIDxGaWx0ZXJCYXI+XG4gICAgICA8dGVtcGxhdGUgI2xlZnQ+XG4gICAgICAgIDxpbnB1dCBcbiAgICAgICAgICB2LW1vZGVsPVwic2VhcmNoUXVlcnlcIlxuICAgICAgICAgIHR5cGU9XCJ0ZXh0XCJcbiAgICAgICAgICA6cGxhY2Vob2xkZXI9XCJgJHskdCgnY29tbW9uLnNlYXJjaCcpfSAkeyR0KCdzdWJqZWN0cy50aXRsZScpfSAo4Z6I4Z+S4Z6Y4Z+E4Z+HLCDhnoDhnrzhnoopLi4uYFwiXG4gICAgICAgICAgY2xhc3M9XCJweC0zLjUgcHktMS41IGJnLXdoaXRlIGJvcmRlciBib3JkZXItc2xhdGUtMjAwIHJvdW5kZWQteGwgdGV4dC14cyB3LTY0IGZvY3VzOm91dGxpbmUtbm9uZSBmb2N1czpyaW5nLTIgZm9jdXM6cmluZy1ibHVlLTUwMC8yMFwiXG4gICAgICAgIC8+XG4gICAgICA8L3RlbXBsYXRlPlxuICAgICAgPHRlbXBsYXRlICNyaWdodD5cbiAgICAgICAgPHNwYW4gY2xhc3M9XCJ0ZXh0LXhzIHRleHQtc2xhdGUtNTAwXCI+e3sgJHQoJ3N1YmplY3RzLnRvdGFsU3ViamVjdHMnKSB9fTogPHN0cm9uZz57eyBmaWx0ZXJlZFN1YmplY3RzLmxlbmd0aCB9fTwvc3Ryb25nPjwvc3Bhbj5cbiAgICAgIDwvdGVtcGxhdGU+XG4gICAgPC9GaWx0ZXJCYXI+XG5cbiAgICA8IS0tIERhdGEgVGFibGUgLS0+XG4gICAgPERhdGFUYWJsZT5cbiAgICAgIDx0ZW1wbGF0ZSAjaGVhZGVyPlxuICAgICAgICA8dHI+XG4gICAgICAgICAgPHRoIGNsYXNzPVwicHktMyBweC00IGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMFwiPnt7ICR0KCdzdWJqZWN0cy5zdWJqZWN0Q29kZScpIH19PC90aD5cbiAgICAgICAgICA8dGggY2xhc3M9XCJweS0zIHB4LTQgZm9udC1ib2xkIHRleHQtc2xhdGUtNzAwXCI+e3sgJHQoJ3N1YmplY3RzLmtobWVyVGl0bGUnKSB9fTwvdGg+XG4gICAgICAgICAgPHRoIGNsYXNzPVwicHktMyBweC00IGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMFwiPnt7ICR0KCdzdWJqZWN0cy5lbmdsaXNoVGl0bGUnKSB9fTwvdGg+XG4gICAgICAgICAgPHRoIGNsYXNzPVwicHktMyBweC00IGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMFwiPuGelOGfkuGemuGel+GfgeGekeGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetjwvdGg+XG4gICAgICAgICAgPHRoIGNsYXNzPVwicHktMyBweC00IGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCB0ZXh0LWNlbnRlclwiPnt7ICR0KCdzdWJqZWN0cy5jcmVkaXRzJykgfX08L3RoPlxuICAgICAgICAgIDx0aCBjbGFzcz1cInB5LTMgcHgtNCBmb250LWJvbGQgdGV4dC1zbGF0ZS03MDAgdGV4dC1jZW50ZXJcIj57eyAkdCgnY29tbW9uLnN0YXR1cycpIH19PC90aD5cbiAgICAgICAgICA8dGggY2xhc3M9XCJweS0zIHB4LTQgZm9udC1ib2xkIHRleHQtc2xhdGUtNzAwIHRleHQtcmlnaHRcIj57eyAkdCgnY29tbW9uLmFjdGlvbnMnKSB9fTwvdGg+XG4gICAgICAgIDwvdHI+XG4gICAgICA8L3RlbXBsYXRlPlxuXG4gICAgICA8dGVtcGxhdGUgI2JvZHk+XG4gICAgICAgIDx0ciB2LWlmPVwiZmlsdGVyZWRTdWJqZWN0cy5sZW5ndGggPT09IDBcIj5cbiAgICAgICAgICA8dGQgY29sc3Bhbj1cIjdcIiBjbGFzcz1cInB5LTEwIHRleHQtY2VudGVyIHRleHQtc2xhdGUtNDAwXCI+e3sgJHQoJ2NvbW1vbi5ub0RhdGEnKSB9fTwvdGQ+XG4gICAgICAgIDwvdHI+XG4gICAgICAgIDx0ciB2LWZvcj1cInMgaW4gZmlsdGVyZWRTdWJqZWN0c1wiIDprZXk9XCJzLmlkIHx8IHMuY29kZVwiIGNsYXNzPVwiaG92ZXI6Ymctc2xhdGUtNTAvNzAgdHJhbnNpdGlvbi1jb2xvcnNcIj5cbiAgICAgICAgICA8dGQgY2xhc3M9XCJweS0zIHB4LTQgZm9udC1tb25vIGZvbnQtYm9sZCB0ZXh0LWJsdWUtNjAwXCI+e3sgcy5jb2RlIH19PC90ZD5cbiAgICAgICAgICA8dGQgY2xhc3M9XCJweS0zIHB4LTQgZm9udC1ib2xkIHRleHQtc2xhdGUtOTAwXCI+e3sgcy5uYW1lS2ggfHwgcy5uYW1lIH19PC90ZD5cbiAgICAgICAgICA8dGQgY2xhc3M9XCJweS0zIHB4LTQgdGV4dC1zbGF0ZS02MDBcIj57eyBzLm5hbWVFbiB8fCBzLmRlc2NyaXB0aW9uIHx8ICctJyB9fTwvdGQ+XG4gICAgICAgICAgPHRkIGNsYXNzPVwicHktMyBweC00XCI+XG4gICAgICAgICAgICA8c3BhbiA6Y2xhc3M9XCJbXG4gICAgICAgICAgICAgICdweC0yLjUgcHktMSByb3VuZGVkLWxnIHRleHQtWzExcHhdIGZvbnQtYm9sZCBpbmxpbmUtZmxleCBpdGVtcy1jZW50ZXIgZ2FwLTEuNScsXG4gICAgICAgICAgICAgIHMudHlwZSA9PT0gJ3NjaWVuY2UnID8gJ2JnLWJsdWUtNTAgdGV4dC1ibHVlLTcwMCBib3JkZXIgYm9yZGVyLWJsdWUtMjAwJyA6XG4gICAgICAgICAgICAgIHMudHlwZSA9PT0gJ2xhbmd1YWdlcycgPyAnYmctcHVycGxlLTUwIHRleHQtcHVycGxlLTcwMCBib3JkZXIgYm9yZGVyLXB1cnBsZS0yMDAnIDpcbiAgICAgICAgICAgICAgcy50eXBlID09PSAnc29jaWFsJyA/ICdiZy1hbWJlci01MCB0ZXh0LWFtYmVyLTcwMCBib3JkZXIgYm9yZGVyLWFtYmVyLTIwMCcgOlxuICAgICAgICAgICAgICBzLnR5cGUgPT09ICdjb3JlJyA/ICdiZy1lbWVyYWxkLTUwIHRleHQtZW1lcmFsZC03MDAgYm9yZGVyIGJvcmRlci1lbWVyYWxkLTIwMCcgOlxuICAgICAgICAgICAgICBzLnR5cGUgPT09ICdlbGVjdGl2ZScgPyAnYmctdGVhbC01MCB0ZXh0LXRlYWwtNzAwIGJvcmRlciBib3JkZXItdGVhbC0yMDAnIDpcbiAgICAgICAgICAgICAgJ2JnLXNsYXRlLTEwMCB0ZXh0LXNsYXRlLTcwMCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCdcbiAgICAgICAgICAgIF1cIj5cbiAgICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJ3LTEuNSBoLTEuNSByb3VuZGVkLWZ1bGxcIiA6Y2xhc3M9XCJbXG4gICAgICAgICAgICAgICAgcy50eXBlID09PSAnc2NpZW5jZScgPyAnYmctYmx1ZS01MDAnIDpcbiAgICAgICAgICAgICAgICBzLnR5cGUgPT09ICdsYW5ndWFnZXMnID8gJ2JnLXB1cnBsZS01MDAnIDpcbiAgICAgICAgICAgICAgICBzLnR5cGUgPT09ICdzb2NpYWwnID8gJ2JnLWFtYmVyLTUwMCcgOlxuICAgICAgICAgICAgICAgIHMudHlwZSA9PT0gJ2NvcmUnID8gJ2JnLWVtZXJhbGQtNTAwJyA6XG4gICAgICAgICAgICAgICAgcy50eXBlID09PSAnZWxlY3RpdmUnID8gJ2JnLXRlYWwtNTAwJyA6XG4gICAgICAgICAgICAgICAgJ2JnLXNsYXRlLTQwMCdcbiAgICAgICAgICAgICAgXVwiPjwvc3Bhbj5cbiAgICAgICAgICAgICAge3sgcy50eXBlTGFiZWwgfHwgJ+GekeGevOGekeGfhScgfX1cbiAgICAgICAgICAgIDwvc3Bhbj5cbiAgICAgICAgICA8L3RkPlxuICAgICAgICAgIDx0ZCBjbGFzcz1cInB5LTMgcHgtNCB0ZXh0LWNlbnRlciBmb250LXNlbWlib2xkIHRleHQtc2xhdGUtNzAwXCI+e3sgcy5jcmVkaXRzIHx8IHMuY3JlZGl0X2hvdXJzIH19IOGemOGfieGfhOGehC/hnp/hnpThn5Lhno/hnrbhnqDhn408L3RkPlxuICAgICAgICAgIDx0ZCBjbGFzcz1cInB5LTMgcHgtNCB0ZXh0LWNlbnRlclwiPlxuICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJweC0yLjUgcHktMC41IHJvdW5kZWQtZnVsbCB0ZXh0LVsxMXB4XSBmb250LWJvbGQgYmctZW1lcmFsZC01MCB0ZXh0LWVtZXJhbGQtNzAwIGJvcmRlciBib3JkZXItZW1lcmFsZC0xMDBcIj5cbiAgICAgICAgICAgICAge3sgJHQoJ2NvbW1vbi5hY3RpdmUnKSB9fVxuICAgICAgICAgICAgPC9zcGFuPlxuICAgICAgICAgIDwvdGQ+XG4gICAgICAgICAgPHRkIGNsYXNzPVwicHktMyBweC00IHRleHQtcmlnaHQgc3BhY2UteC0xLjUgd2hpdGVzcGFjZS1ub3dyYXBcIj5cbiAgICAgICAgICAgIDxidXR0b24gQGNsaWNrPVwib3BlbkVkaXRNb2RhbChzKVwiIGNsYXNzPVwicHgtMi41IHB5LTEgYmctYmx1ZS01MCB0ZXh0LWJsdWUtNjAwIHJvdW5kZWQtbGcgdGV4dC14cyBmb250LXNlbWlib2xkIGhvdmVyOmJnLWJsdWUtMTAwIHRyYW5zaXRpb25cIj5cbiAgICAgICAgICAgICAge3sgJHQoJ2NvbW1vbi5lZGl0JykgfX1cbiAgICAgICAgICAgIDwvYnV0dG9uPlxuICAgICAgICAgICAgPGJ1dHRvbiBAY2xpY2s9XCJoYW5kbGVEZWxldGUocylcIiBjbGFzcz1cInB4LTIgcHktMSBiZy1yb3NlLTUwIHRleHQtcm9zZS02MDAgcm91bmRlZC1sZyB0ZXh0LXhzIGZvbnQtc2VtaWJvbGQgaG92ZXI6Ymctcm9zZS0xMDAgdHJhbnNpdGlvblwiPlxuICAgICAgICAgICAgICB7eyAkdCgnY29tbW9uLmRlbGV0ZScpIH19XG4gICAgICAgICAgICA8L2J1dHRvbj5cbiAgICAgICAgICA8L3RkPlxuICAgICAgICA8L3RyPlxuICAgICAgPC90ZW1wbGF0ZT5cbiAgICA8L0RhdGFUYWJsZT5cblxuICAgIDwhLS0gTW9kYWwgMTogQ3JlYXRlIFN1YmplY3QgTW9kYWwgLS0+XG4gICAgPGRpdiB2LWlmPVwic2hvd0NyZWF0ZU1vZGFsXCIgY2xhc3M9XCJmaXhlZCBpbnNldC0wIHotNTAgZmxleCBpdGVtcy1jZW50ZXIganVzdGlmeS1jZW50ZXIgcC00IGJnLXNsYXRlLTkwMC81MCBiYWNrZHJvcC1ibHVyLXNtXCI+XG4gICAgICA8ZGl2IGNsYXNzPVwiYmctd2hpdGUgcm91bmRlZC0yeGwgdy1mdWxsIG1heC13LWxnIHNoYWRvdy0yeGwgYm9yZGVyIGJvcmRlci1zbGF0ZS0xMDAgb3ZlcmZsb3ctaGlkZGVuIGFuaW1hdGUtaW4gZmFkZS1pbiB6b29tLWluLTk1IGR1cmF0aW9uLTE1MFwiPlxuICAgICAgICA8ZGl2IGNsYXNzPVwicHgtNiBweS00IGJvcmRlci1iIGJvcmRlci1zbGF0ZS0xMDAgZmxleCBpdGVtcy1jZW50ZXIganVzdGlmeS1iZXR3ZWVuIGJnLXNsYXRlLTUwLzUwXCI+XG4gICAgICAgICAgPGgzIGNsYXNzPVwiZm9udC1ib2xkIHRleHQtc2xhdGUtODAwIHRleHQtYmFzZSBmbGV4IGl0ZW1zLWNlbnRlciBnYXAtMlwiPlxuICAgICAgICAgICAgPHNwYW4gY2xhc3M9XCJ3LTIuNSBoLTIuNSByb3VuZGVkLWZ1bGwgYmctYmx1ZS02MDBcIj48L3NwYW4+XG4gICAgICAgICAgICArIOGelOGek+GfkuGekOGfguGemOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGekOGfkuGemOGeuFxuICAgICAgICAgIDwvaDM+XG4gICAgICAgICAgPGJ1dHRvbiBAY2xpY2s9XCJzaG93Q3JlYXRlTW9kYWwgPSBmYWxzZVwiIGNsYXNzPVwidGV4dC1zbGF0ZS00MDAgaG92ZXI6dGV4dC1zbGF0ZS02MDAgdGV4dC1sZ1wiPiZ0aW1lczs8L2J1dHRvbj5cbiAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgPGZvcm0gQHN1Ym1pdC5wcmV2ZW50PVwic3VibWl0Q3JlYXRlU3ViamVjdFwiIGNsYXNzPVwicC02IHNwYWNlLXktNFwiPlxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJncmlkIGdyaWQtY29scy0yIGdhcC00XCI+XG4gICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJibG9jayB0ZXh0LXhzIGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCBtYi0xXCI+4Z6A4Z684Z6K4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62IChDb2RlKSAqPC9sYWJlbD5cbiAgICAgICAgICAgICAgPGlucHV0IHYtbW9kZWw9XCJjcmVhdGVGb3JtLmNvZGVcIiB0eXBlPVwidGV4dFwiIHJlcXVpcmVkIHBsYWNlaG9sZGVyPVwi4Z6nLiBNVEgtMDFcIiBjbGFzcz1cInctZnVsbCBweC0zIHB5LTIgYmctc2xhdGUtNTAgYm9yZGVyIGJvcmRlci1zbGF0ZS0yMDAgcm91bmRlZC14bCB0ZXh0LXNtIGZvbnQtbW9ubyBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjAgdXBwZXJjYXNlXCI+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz1cImJsb2NrIHRleHQteHMgZm9udC1ib2xkIHRleHQtc2xhdGUtNzAwIG1iLTFcIj7hnpjhn4nhn4ThnoThnp/hnrfhnoDhn5Lhnp/hnrYv4Z6f4Z6U4Z+S4Z6P4Z624Z6g4Z+NICo8L2xhYmVsPlxuICAgICAgICAgICAgICA8aW5wdXQgdi1tb2RlbC5udW1iZXI9XCJjcmVhdGVGb3JtLmNyZWRpdF9ob3Vyc1wiIHR5cGU9XCJudW1iZXJcIiBtaW49XCIxXCIgbWF4PVwiMTVcIiByZXF1aXJlZCBjbGFzcz1cInctZnVsbCBweC0zIHB5LTIgYmctc2xhdGUtNTAgYm9yZGVyIGJvcmRlci1zbGF0ZS0yMDAgcm91bmRlZC14bCB0ZXh0LXNtIGZvY3VzOm91dGxpbmUtbm9uZSBmb2N1czpyaW5nLTIgZm9jdXM6cmluZy1ibHVlLTUwMC8yMFwiPlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgPGxhYmVsIGNsYXNzPVwiYmxvY2sgdGV4dC14cyBmb250LWJvbGQgdGV4dC1zbGF0ZS03MDAgbWItMVwiPuGeiOGfkuGemOGfhOGfh+GemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiAo4Z6X4Z624Z6f4Z624Z6B4Z+S4Z6Y4Z+C4Z6aKSAqPC9sYWJlbD5cbiAgICAgICAgICAgIDxpbnB1dCB2LW1vZGVsPVwiY3JlYXRlRm9ybS5uYW1lXCIgdHlwZT1cInRleHRcIiByZXF1aXJlZCBwbGFjZWhvbGRlcj1cIuGepy4g4Z6C4Z6O4Z634Z6P4Z6c4Z634Z6R4Z+S4Z6Z4Z62XCIgY2xhc3M9XCJ3LWZ1bGwgcHgtMyBweS0yIGJnLXNsYXRlLTUwIGJvcmRlciBib3JkZXItc2xhdGUtMjAwIHJvdW5kZWQteGwgdGV4dC1zbSBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJibG9jayB0ZXh0LXhzIGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCBtYi0xXCI+4Z6I4Z+S4Z6Y4Z+E4Z+H4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62ICjhnqLhnoThn4vhnoLhn5Lhnpvhn4Hhnp8gLyDhnoDhnrbhnprhnpbhnrfhnpbhno7hn4zhnpPhnrYpPC9sYWJlbD5cbiAgICAgICAgICAgIDxpbnB1dCB2LW1vZGVsPVwiY3JlYXRlRm9ybS5kZXNjcmlwdGlvblwiIHR5cGU9XCJ0ZXh0XCIgcGxhY2Vob2xkZXI9XCLhnqcuIE1hdGhlbWF0aWNzXCIgY2xhc3M9XCJ3LWZ1bGwgcHgtMyBweS0yIGJnLXNsYXRlLTUwIGJvcmRlciBib3JkZXItc2xhdGUtMjAwIHJvdW5kZWQteGwgdGV4dC1zbSBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJncmlkIGdyaWQtY29scy0yIGdhcC00XCI+XG4gICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJibG9jayB0ZXh0LXhzIGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCBtYi0xXCI+4Z6U4Z+S4Z6a4Z6X4Z+B4Z6R4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62ICo8L2xhYmVsPlxuICAgICAgICAgICAgICA8c2VsZWN0IHYtbW9kZWw9XCJjcmVhdGVGb3JtLnN1YmplY3RfdHlwZVwiIHJlcXVpcmVkIGNsYXNzPVwidy1mdWxsIHB4LTMgcHktMiBiZy1zbGF0ZS01MCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCByb3VuZGVkLXhsIHRleHQteHMgZm9udC1zZW1pYm9sZCBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiY29yZVwiPuKtkCDhnpjhnrvhnoHhnpzhnrfhnofhn5LhnofhnrbhnoLhn4ThnpsgLyDhnp/hn5LhnpPhnrzhnps8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwic2NpZW5jZVwiPvCflKwg4Z6c4Z634Z6R4Z+S4Z6Z4Z624Z6f4Z624Z6f4Z+S4Z6P4Z+S4Z6a4Z6W4Z634Z6PIChTY2llbmNlKTwvb3B0aW9uPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCJzb2NpYWxcIj7wn4yNIOGenOGet+GekeGfkuGemeGetuGen+GetuGen+GfkuGej+GfkuGemuGen+GehOGfkuGeguGemCAoU29jaWFsIFNjaWVuY2UpPC9vcHRpb24+XG4gICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT1cImxhbmd1YWdlc1wiPvCfl6PvuI8g4Z6X4Z624Z6f4Z624Z6U4Z6a4Z6R4Z+B4Z6fIC8g4Z6X4Z624Z6f4Z62PC9vcHRpb24+XG4gICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT1cImVsZWN0aXZlXCI+8J+OqCDhnpjhnrvhnoHhnpzhnrfhnofhn5Lhnofhnrbhnofhn5Lhnprhnr7hnp/hnprhnr7hnp8gLyDhnpThn4bhno7hnrfhnpM8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiZ2VuZXJhbFwiPvCfk5og4Z6R4Z684Z6R4Z+FIChHZW5lcmFsKTwvb3B0aW9uPlxuICAgICAgICAgICAgICA8L3NlbGVjdD5cbiAgICAgICAgICAgIDwvZGl2PlxuXG4gICAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJibG9jayB0ZXh0LXhzIGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCBtYi0xXCI+4Z6A4Z6Y4Z+S4Z6a4Z634Z6P4Z6Q4Z+S4Z6T4Z624Z6A4Z+L4Z6K4Z+C4Z6b4Z6P4Z+S4Z6a4Z684Z6c4Z6a4Z+A4Z6TPC9sYWJlbD5cbiAgICAgICAgICAgICAgPHNlbGVjdCB2LW1vZGVsPVwiY3JlYXRlRm9ybS5ncmFkZV9sZXZlbFwiIGNsYXNzPVwidy1mdWxsIHB4LTMgcHktMiBiZy1zbGF0ZS01MCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCByb3VuZGVkLXhsIHRleHQteHMgZm9udC1zZW1pYm9sZCBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiNy0xMlwiPuGekOGfkuGek+GetuGegOGfi+GekeGeuCDhn6cg4Z6K4Z6b4Z+LIOGfoeGfoiAo4Z6R4Z684Z6R4Z+FKTwvb3B0aW9uPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCI3LTlcIj7hnqLhnpPhnrvhnpzhnrfhnpHhn5Lhnpnhnrbhnpvhn5DhnpkgKOGfpyAtIOGfqSk8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiMTAtMTJcIj7hnpzhnrfhnpHhn5Lhnpnhnrbhnpvhn5DhnpkgKOGfoeGfoCAtIOGfoeGfoik8L29wdGlvbj5cbiAgICAgICAgICAgICAgPC9zZWxlY3Q+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJmbGV4IGl0ZW1zLWNlbnRlciBqdXN0aWZ5LWVuZCBnYXAtMiBwdC0zIGJvcmRlci10IGJvcmRlci1zbGF0ZS0xMDBcIj5cbiAgICAgICAgICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIEBjbGljaz1cInNob3dDcmVhdGVNb2RhbCA9IGZhbHNlXCIgY2xhc3M9XCJweC00IHB5LTIgYmctc2xhdGUtMTAwIHRleHQtc2xhdGUtNjAwIHJvdW5kZWQteGwgdGV4dC14cyBmb250LXNlbWlib2xkIGhvdmVyOmJnLXNsYXRlLTIwMFwiPlxuICAgICAgICAgICAgICDhnpThn4Thn4fhnpThnoThn4tcbiAgICAgICAgICAgIDwvYnV0dG9uPlxuICAgICAgICAgICAgPGJ1dHRvbiB0eXBlPVwic3VibWl0XCIgOmRpc2FibGVkPVwiaXNTdWJtaXR0aW5nXCIgY2xhc3M9XCJweC01IHB5LTIgYmctYmx1ZS02MDAgdGV4dC13aGl0ZSByb3VuZGVkLXhsIHRleHQteHMgZm9udC1ib2xkIGhvdmVyOmJnLWJsdWUtNzAwIHRyYW5zaXRpb24gZGlzYWJsZWQ6b3BhY2l0eS01MFwiPlxuICAgICAgICAgICAgICA8c3BhbiB2LWlmPVwiaXNTdWJtaXR0aW5nXCI+4Z6A4Z+G4Z6W4Z674Z6E4Z6a4Z6A4Z+S4Z6f4Z624Z6R4Z674Z6ALi4uPC9zcGFuPlxuICAgICAgICAgICAgICA8c3BhbiB2LWVsc2U+4Z6a4Z6A4Z+S4Z6f4Z624Z6R4Z674Z6A4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62PC9zcGFuPlxuICAgICAgICAgICAgPC9idXR0b24+XG4gICAgICAgICAgPC9kaXY+XG4gICAgICAgIDwvZm9ybT5cbiAgICAgIDwvZGl2PlxuICAgIDwvZGl2PlxuXG4gICAgPCEtLSBNb2RhbCAyOiBFZGl0IFN1YmplY3QgTW9kYWwgLS0+XG4gICAgPGRpdiB2LWlmPVwic2hvd0VkaXRNb2RhbFwiIGNsYXNzPVwiZml4ZWQgaW5zZXQtMCB6LTUwIGZsZXggaXRlbXMtY2VudGVyIGp1c3RpZnktY2VudGVyIHAtNCBiZy1zbGF0ZS05MDAvNTAgYmFja2Ryb3AtYmx1ci1zbVwiPlxuICAgICAgPGRpdiBjbGFzcz1cImJnLXdoaXRlIHJvdW5kZWQtMnhsIHctZnVsbCBtYXgtdy1sZyBzaGFkb3ctMnhsIGJvcmRlciBib3JkZXItc2xhdGUtMTAwIG92ZXJmbG93LWhpZGRlbiBhbmltYXRlLWluIGZhZGUtaW4gem9vbS1pbi05NSBkdXJhdGlvbi0xNTBcIj5cbiAgICAgICAgPGRpdiBjbGFzcz1cInB4LTYgcHktNCBib3JkZXItYiBib3JkZXItc2xhdGUtMTAwIGZsZXggaXRlbXMtY2VudGVyIGp1c3RpZnktYmV0d2VlbiBiZy1zbGF0ZS01MC81MFwiPlxuICAgICAgICAgIDxoMyBjbGFzcz1cImZvbnQtYm9sZCB0ZXh0LXNsYXRlLTgwMCB0ZXh0LWJhc2UgZmxleCBpdGVtcy1jZW50ZXIgZ2FwLTJcIj5cbiAgICAgICAgICAgIDxzcGFuIGNsYXNzPVwidy0yLjUgaC0yLjUgcm91bmRlZC1mdWxsIGJnLWFtYmVyLTUwMFwiPjwvc3Bhbj5cbiAgICAgICAgICAgIOGegOGfguGelOGfkuGemuGfguGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiB7eyBzZWxlY3RlZFN1YmplY3Q/Lm5hbWUgfX1cbiAgICAgICAgICA8L2gzPlxuICAgICAgICAgIDxidXR0b24gQGNsaWNrPVwic2hvd0VkaXRNb2RhbCA9IGZhbHNlXCIgY2xhc3M9XCJ0ZXh0LXNsYXRlLTQwMCBob3Zlcjp0ZXh0LXNsYXRlLTYwMCB0ZXh0LWxnXCI+JnRpbWVzOzwvYnV0dG9uPlxuICAgICAgICA8L2Rpdj5cblxuICAgICAgICA8Zm9ybSBAc3VibWl0LnByZXZlbnQ9XCJzdWJtaXRVcGRhdGVTdWJqZWN0XCIgY2xhc3M9XCJwLTYgc3BhY2UteS00XCI+XG4gICAgICAgICAgPGRpdiBjbGFzcz1cImdyaWQgZ3JpZC1jb2xzLTIgZ2FwLTRcIj5cbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz1cImJsb2NrIHRleHQteHMgZm9udC1ib2xkIHRleHQtc2xhdGUtNzAwIG1iLTFcIj7hnoDhnrzhnorhnpjhnrvhnoHhnpzhnrfhnofhn5LhnofhnrYgKENvZGUpICo8L2xhYmVsPlxuICAgICAgICAgICAgICA8aW5wdXQgdi1tb2RlbD1cImVkaXRGb3JtLmNvZGVcIiB0eXBlPVwidGV4dFwiIHJlcXVpcmVkIGNsYXNzPVwidy1mdWxsIHB4LTMgcHktMiBiZy1zbGF0ZS01MCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCByb3VuZGVkLXhsIHRleHQtc20gZm9udC1tb25vIGZvY3VzOm91dGxpbmUtbm9uZSBmb2N1czpyaW5nLTIgZm9jdXM6cmluZy1ibHVlLTUwMC8yMCB1cHBlcmNhc2VcIj5cbiAgICAgICAgICAgIDwvZGl2PlxuICAgICAgICAgICAgPGRpdj5cbiAgICAgICAgICAgICAgPGxhYmVsIGNsYXNzPVwiYmxvY2sgdGV4dC14cyBmb250LWJvbGQgdGV4dC1zbGF0ZS03MDAgbWItMVwiPuGemOGfieGfhOGehOGen+Get+GegOGfkuGen+Geti/hnp/hnpThn5Lhno/hnrbhnqDhn40gKjwvbGFiZWw+XG4gICAgICAgICAgICAgIDxpbnB1dCB2LW1vZGVsLm51bWJlcj1cImVkaXRGb3JtLmNyZWRpdF9ob3Vyc1wiIHR5cGU9XCJudW1iZXJcIiBtaW49XCIxXCIgbWF4PVwiMTVcIiByZXF1aXJlZCBjbGFzcz1cInctZnVsbCBweC0zIHB5LTIgYmctc2xhdGUtNTAgYm9yZGVyIGJvcmRlci1zbGF0ZS0yMDAgcm91bmRlZC14bCB0ZXh0LXNtIGZvY3VzOm91dGxpbmUtbm9uZSBmb2N1czpyaW5nLTIgZm9jdXM6cmluZy1ibHVlLTUwMC8yMFwiPlxuICAgICAgICAgICAgPC9kaXY+XG4gICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICA8ZGl2PlxuICAgICAgICAgICAgPGxhYmVsIGNsYXNzPVwiYmxvY2sgdGV4dC14cyBmb250LWJvbGQgdGV4dC1zbGF0ZS03MDAgbWItMVwiPuGeiOGfkuGemOGfhOGfh+GemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiAo4Z6X4Z624Z6f4Z624Z6B4Z+S4Z6Y4Z+C4Z6aKSAqPC9sYWJlbD5cbiAgICAgICAgICAgIDxpbnB1dCB2LW1vZGVsPVwiZWRpdEZvcm0ubmFtZVwiIHR5cGU9XCJ0ZXh0XCIgcmVxdWlyZWQgY2xhc3M9XCJ3LWZ1bGwgcHgtMyBweS0yIGJnLXNsYXRlLTUwIGJvcmRlciBib3JkZXItc2xhdGUtMjAwIHJvdW5kZWQteGwgdGV4dC1zbSBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICA8bGFiZWwgY2xhc3M9XCJibG9jayB0ZXh0LXhzIGZvbnQtYm9sZCB0ZXh0LXNsYXRlLTcwMCBtYi0xXCI+4Z6I4Z+S4Z6Y4Z+E4Z+H4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62ICjhnqLhnoThn4vhnoLhn5Lhnpvhn4Hhnp8gLyDhnoDhnrbhnprhnpbhnrfhnpbhno7hn4zhnpPhnrYpPC9sYWJlbD5cbiAgICAgICAgICAgIDxpbnB1dCB2LW1vZGVsPVwiZWRpdEZvcm0uZGVzY3JpcHRpb25cIiB0eXBlPVwidGV4dFwiIGNsYXNzPVwidy1mdWxsIHB4LTMgcHktMiBiZy1zbGF0ZS01MCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCByb3VuZGVkLXhsIHRleHQtc20gZm9jdXM6b3V0bGluZS1ub25lIGZvY3VzOnJpbmctMiBmb2N1czpyaW5nLWJsdWUtNTAwLzIwXCI+XG4gICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICA8ZGl2IGNsYXNzPVwiZ3JpZCBncmlkLWNvbHMtMiBnYXAtNFwiPlxuICAgICAgICAgICAgPGRpdj5cbiAgICAgICAgICAgICAgPGxhYmVsIGNsYXNzPVwiYmxvY2sgdGV4dC14cyBmb250LWJvbGQgdGV4dC1zbGF0ZS03MDAgbWItMVwiPuGelOGfkuGemuGel+GfgeGekeGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiAqPC9sYWJlbD5cbiAgICAgICAgICAgICAgPHNlbGVjdCB2LW1vZGVsPVwiZWRpdEZvcm0uc3ViamVjdF90eXBlXCIgcmVxdWlyZWQgY2xhc3M9XCJ3LWZ1bGwgcHgtMyBweS0yIGJnLXNsYXRlLTUwIGJvcmRlciBib3JkZXItc2xhdGUtMjAwIHJvdW5kZWQteGwgdGV4dC14cyBmb250LXNlbWlib2xkIGZvY3VzOm91dGxpbmUtbm9uZSBmb2N1czpyaW5nLTIgZm9jdXM6cmluZy1ibHVlLTUwMC8yMFwiPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCJjb3JlXCI+4q2QIOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGeguGfhOGemyAvIOGen+GfkuGek+GevOGemzwvb3B0aW9uPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCJzY2llbmNlXCI+8J+UrCDhnpzhnrfhnpHhn5Lhnpnhnrbhnp/hnrbhnp/hn5Lhno/hn5Lhnprhnpbhnrfhno8gKFNjaWVuY2UpPC9vcHRpb24+XG4gICAgICAgICAgICAgICAgPG9wdGlvbiB2YWx1ZT1cInNvY2lhbFwiPvCfjI0g4Z6c4Z634Z6R4Z+S4Z6Z4Z624Z6f4Z624Z6f4Z+S4Z6P4Z+S4Z6a4Z6f4Z6E4Z+S4Z6C4Z6YIChTb2NpYWwgU2NpZW5jZSk8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwibGFuZ3VhZ2VzXCI+8J+Xo++4jyDhnpfhnrbhnp/hnrbhnpThnprhnpHhn4Hhnp8gLyDhnpfhnrbhnp/hnrY8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiZWxlY3RpdmVcIj7wn46oIOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGeh+GfkuGemuGevuGen+GemuGevuGenyAvIOGelOGfhuGejuGet+Gekzwvb3B0aW9uPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCJnZW5lcmFsXCI+8J+TmiDhnpHhnrzhnpHhn4UgKEdlbmVyYWwpPC9vcHRpb24+XG4gICAgICAgICAgICAgIDwvc2VsZWN0PlxuICAgICAgICAgICAgPC9kaXY+XG5cbiAgICAgICAgICAgIDxkaXY+XG4gICAgICAgICAgICAgIDxsYWJlbCBjbGFzcz1cImJsb2NrIHRleHQteHMgZm9udC1ib2xkIHRleHQtc2xhdGUtNzAwIG1iLTFcIj7hnoDhnpjhn5Lhnprhnrfhno/hnpDhn5LhnpPhnrbhnoDhn4vhnorhn4Lhnpvhno/hn5Lhnprhnrzhnpzhnprhn4DhnpM8L2xhYmVsPlxuICAgICAgICAgICAgICA8c2VsZWN0IHYtbW9kZWw9XCJlZGl0Rm9ybS5ncmFkZV9sZXZlbFwiIGNsYXNzPVwidy1mdWxsIHB4LTMgcHktMiBiZy1zbGF0ZS01MCBib3JkZXIgYm9yZGVyLXNsYXRlLTIwMCByb3VuZGVkLXhsIHRleHQteHMgZm9udC1zZW1pYm9sZCBmb2N1czpvdXRsaW5lLW5vbmUgZm9jdXM6cmluZy0yIGZvY3VzOnJpbmctYmx1ZS01MDAvMjBcIj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiNy0xMlwiPuGekOGfkuGek+GetuGegOGfi+GekeGeuCDhn6cg4Z6K4Z6b4Z+LIOGfoeGfoiAo4Z6R4Z684Z6R4Z+FKTwvb3B0aW9uPlxuICAgICAgICAgICAgICAgIDxvcHRpb24gdmFsdWU9XCI3LTlcIj7hnqLhnpPhnrvhnpzhnrfhnpHhn5Lhnpnhnrbhnpvhn5DhnpkgKOGfpyAtIOGfqSk8L29wdGlvbj5cbiAgICAgICAgICAgICAgICA8b3B0aW9uIHZhbHVlPVwiMTAtMTJcIj7hnpzhnrfhnpHhn5Lhnpnhnrbhnpvhn5DhnpkgKOGfoeGfoCAtIOGfoeGfoik8L29wdGlvbj5cbiAgICAgICAgICAgICAgPC9zZWxlY3Q+XG4gICAgICAgICAgICA8L2Rpdj5cbiAgICAgICAgICA8L2Rpdj5cblxuICAgICAgICAgIDxkaXYgY2xhc3M9XCJmbGV4IGl0ZW1zLWNlbnRlciBqdXN0aWZ5LWVuZCBnYXAtMiBwdC0zIGJvcmRlci10IGJvcmRlci1zbGF0ZS0xMDBcIj5cbiAgICAgICAgICAgIDxidXR0b24gdHlwZT1cImJ1dHRvblwiIEBjbGljaz1cInNob3dFZGl0TW9kYWwgPSBmYWxzZVwiIGNsYXNzPVwicHgtNCBweS0yIGJnLXNsYXRlLTEwMCB0ZXh0LXNsYXRlLTYwMCByb3VuZGVkLXhsIHRleHQteHMgZm9udC1zZW1pYm9sZCBob3ZlcjpiZy1zbGF0ZS0yMDBcIj5cbiAgICAgICAgICAgICAg4Z6U4Z+E4Z+H4Z6U4Z6E4Z+LXG4gICAgICAgICAgICA8L2J1dHRvbj5cbiAgICAgICAgICAgIDxidXR0b24gdHlwZT1cInN1Ym1pdFwiIDpkaXNhYmxlZD1cImlzU3VibWl0dGluZ1wiIGNsYXNzPVwicHgtNSBweS0yIGJnLWJsdWUtNjAwIHRleHQtd2hpdGUgcm91bmRlZC14bCB0ZXh0LXhzIGZvbnQtYm9sZCBob3ZlcjpiZy1ibHVlLTcwMCB0cmFuc2l0aW9uIGRpc2FibGVkOm9wYWNpdHktNTBcIj5cbiAgICAgICAgICAgICAgPHNwYW4gdi1pZj1cImlzU3VibWl0dGluZ1wiPuGegOGfhuGeluGeu+GehOGemuGegOGfkuGen+GetuGekeGeu+GegC4uLjwvc3Bhbj5cbiAgICAgICAgICAgICAgPHNwYW4gdi1lbHNlPuGemuGegOGfkuGen+GetuGekeGeu+GegOGegOGetuGemuGegOGfguGelOGfkuGemuGfgjwvc3Bhbj5cbiAgICAgICAgICAgIDwvYnV0dG9uPlxuICAgICAgICAgIDwvZGl2PlxuICAgICAgICA8L2Zvcm0+XG4gICAgICA8L2Rpdj5cbiAgICA8L2Rpdj5cbiAgPC9kaXY+XG48L3RlbXBsYXRlPlxuXG48c2NyaXB0IHNldHVwPlxuaW1wb3J0IHsgcmVmLCBjb21wdXRlZCwgb25Nb3VudGVkIH0gZnJvbSAndnVlJztcbmltcG9ydCB7IHVzZUkxOG4gfSBmcm9tICd2dWUtaTE4bic7XG5pbXBvcnQgYXhpb3MgZnJvbSAnYXhpb3MnO1xuaW1wb3J0IFBhZ2VIZWFkZXIgZnJvbSAnLi4vY29tcG9uZW50cy9QYWdlSGVhZGVyLnZ1ZSc7XG5pbXBvcnQgVGFiTmF2IGZyb20gJy4uL2NvbXBvbmVudHMvVGFiTmF2LnZ1ZSc7XG5pbXBvcnQgRW50aXR5QmFubmVyIGZyb20gJy4uL2NvbXBvbmVudHMvRW50aXR5QmFubmVyLnZ1ZSc7XG5pbXBvcnQgU3RhdENhcmQgZnJvbSAnLi4vY29tcG9uZW50cy9TdGF0Q2FyZC52dWUnO1xuaW1wb3J0IEZpbHRlckJhciBmcm9tICcuLi9jb21wb25lbnRzL0ZpbHRlckJhci52dWUnO1xuaW1wb3J0IERhdGFUYWJsZSBmcm9tICcuLi9jb21wb25lbnRzL0RhdGFUYWJsZS52dWUnO1xuXG5jb25zdCB7IHQgfSA9IHVzZUkxOG4oKTtcblxuY29uc3QgYWN0aXZlVGFiID0gcmVmKCdhbGwnKTtcbmNvbnN0IHNlYXJjaFF1ZXJ5ID0gcmVmKCcnKTtcbmNvbnN0IHN1YmplY3RzTGlzdCA9IHJlZihbXSk7XG5cbmNvbnN0IHNob3dDcmVhdGVNb2RhbCA9IHJlZihmYWxzZSk7XG5jb25zdCBzaG93RWRpdE1vZGFsID0gcmVmKGZhbHNlKTtcbmNvbnN0IHNlbGVjdGVkU3ViamVjdCA9IHJlZihudWxsKTtcbmNvbnN0IGlzU3VibWl0dGluZyA9IHJlZihmYWxzZSk7XG5cbmNvbnN0IGNyZWF0ZUZvcm0gPSByZWYoe1xuICBjb2RlOiAnJyxcbiAgbmFtZTogJycsXG4gIHN1YmplY3RfdHlwZTogJ2NvcmUnLFxuICBkZXNjcmlwdGlvbjogJycsXG4gIGNyZWRpdF9ob3VyczogMyxcbiAgZ3JhZGVfbGV2ZWw6ICc3LTEyJyxcbn0pO1xuXG5jb25zdCBlZGl0Rm9ybSA9IHJlZih7XG4gIGNvZGU6ICcnLFxuICBuYW1lOiAnJyxcbiAgc3ViamVjdF90eXBlOiAnY29yZScsXG4gIGRlc2NyaXB0aW9uOiAnJyxcbiAgY3JlZGl0X2hvdXJzOiAzLFxuICBncmFkZV9sZXZlbDogJzctMTInLFxufSk7XG5cbmNvbnN0IGZldGNoU3ViamVjdHMgPSBhc3luYyAoKSA9PiB7XG4gIHRyeSB7XG4gICAgY29uc3QgcmVzID0gYXdhaXQgYXhpb3MuZ2V0KCcvYXBpL2NvdXJzZS1zdWJqZWN0cycpO1xuICAgIHN1YmplY3RzTGlzdC52YWx1ZSA9IHJlcy5kYXRhLnN1YmplY3RzIHx8IFtdO1xuICB9IGNhdGNoIChlcnIpIHtcbiAgICBjb25zb2xlLmVycm9yKCdGYWlsZWQgdG8gZmV0Y2ggc3ViamVjdHM6JywgZXJyKTtcbiAgfVxufTtcblxub25Nb3VudGVkKCgpID0+IHtcbiAgZmV0Y2hTdWJqZWN0cygpO1xufSk7XG5cbmNvbnN0IGZpbHRlcmVkU3ViamVjdHMgPSBjb21wdXRlZCgoKSA9PiB7XG4gIHJldHVybiBzdWJqZWN0c0xpc3QudmFsdWUuZmlsdGVyKHMgPT4ge1xuICAgIGNvbnN0IG1hdGNoZXNUYWIgPSBhY3RpdmVUYWIudmFsdWUgPT09ICdhbGwnIHx8IHMudHlwZSA9PT0gYWN0aXZlVGFiLnZhbHVlIHx8IHMuc3ViamVjdF90eXBlID09PSBhY3RpdmVUYWIudmFsdWU7XG4gICAgY29uc3QgcSA9IHNlYXJjaFF1ZXJ5LnZhbHVlLnRvTG93ZXJDYXNlKCk7XG4gICAgY29uc3QgbWF0Y2hlc1NlYXJjaCA9ICFxIHx8IFxuICAgICAgKHMubmFtZUtoICYmIHMubmFtZUtoLnRvTG93ZXJDYXNlKCkuaW5jbHVkZXMocSkpIHx8IFxuICAgICAgKHMubmFtZSAmJiBzLm5hbWUudG9Mb3dlckNhc2UoKS5pbmNsdWRlcyhxKSkgfHwgXG4gICAgICAocy5uYW1lRW4gJiYgcy5uYW1lRW4udG9Mb3dlckNhc2UoKS5pbmNsdWRlcyhxKSkgfHwgXG4gICAgICBzLmNvZGUudG9Mb3dlckNhc2UoKS5pbmNsdWRlcyhxKTtcbiAgICByZXR1cm4gbWF0Y2hlc1RhYiAmJiBtYXRjaGVzU2VhcmNoO1xuICB9KTtcbn0pO1xuXG5jb25zdCB0b3RhbENyZWRpdHMgPSBjb21wdXRlZCgoKSA9PiBzdWJqZWN0c0xpc3QudmFsdWUucmVkdWNlKChhLCBzKSA9PiBhICsgKE51bWJlcihzLmNyZWRpdHMgfHwgcy5jcmVkaXRfaG91cnMpIHx8IDApLCAwKSk7XG5cbmNvbnN0IG9wZW5DcmVhdGVNb2RhbCA9ICgpID0+IHtcbiAgY3JlYXRlRm9ybS52YWx1ZSA9IHtcbiAgICBjb2RlOiAnJyxcbiAgICBuYW1lOiAnJyxcbiAgICBzdWJqZWN0X3R5cGU6ICdjb3JlJyxcbiAgICBkZXNjcmlwdGlvbjogJycsXG4gICAgY3JlZGl0X2hvdXJzOiAzLFxuICAgIGdyYWRlX2xldmVsOiAnNy0xMicsXG4gIH07XG4gIHNob3dDcmVhdGVNb2RhbC52YWx1ZSA9IHRydWU7XG59O1xuXG5jb25zdCBvcGVuRWRpdE1vZGFsID0gKHMpID0+IHtcbiAgc2VsZWN0ZWRTdWJqZWN0LnZhbHVlID0gcztcbiAgZWRpdEZvcm0udmFsdWUgPSB7XG4gICAgY29kZTogcy5jb2RlLFxuICAgIG5hbWU6IHMubmFtZUtoIHx8IHMubmFtZSxcbiAgICBzdWJqZWN0X3R5cGU6IHMuc3ViamVjdF90eXBlIHx8IHMudHlwZSB8fCAnY29yZScsXG4gICAgZGVzY3JpcHRpb246IHMubmFtZUVuIHx8IHMuZGVzY3JpcHRpb24gfHwgJycsXG4gICAgY3JlZGl0X2hvdXJzOiBzLmNyZWRpdHMgfHwgcy5jcmVkaXRfaG91cnMgfHwgMyxcbiAgICBncmFkZV9sZXZlbDogcy5ncmFkZV9sZXZlbCB8fCAnNy0xMicsXG4gIH07XG4gIHNob3dFZGl0TW9kYWwudmFsdWUgPSB0cnVlO1xufTtcblxuY29uc3Qgc3VibWl0Q3JlYXRlU3ViamVjdCA9IGFzeW5jICgpID0+IHtcbiAgaXNTdWJtaXR0aW5nLnZhbHVlID0gdHJ1ZTtcbiAgdHJ5IHtcbiAgICBjb25zdCByZXMgPSBhd2FpdCBheGlvcy5wb3N0KCcvYXBpL2NvdXJzZS1zdWJqZWN0cycsIGNyZWF0ZUZvcm0udmFsdWUpO1xuICAgIHNob3dDcmVhdGVNb2RhbC52YWx1ZSA9IGZhbHNlO1xuICAgIGFsZXJ0KHJlcy5kYXRhLm1lc3NhZ2UgfHwgJ+GelOGetuGek+GelOGehOGfkuGegOGevuGej+GemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGekOGfkuGemOGeuOGeh+GfhOGeguGeh+GfkOGemSEnKTtcbiAgICBhd2FpdCBmZXRjaFN1YmplY3RzKCk7XG4gIH0gY2F0Y2ggKGVycikge1xuICAgIGFsZXJ0KGVyci5yZXNwb25zZT8uZGF0YT8ubWVzc2FnZSB8fCAn4Z6Y4Z624Z6T4Z6U4Z6J4Z+S4Z6g4Z624Z6A4Z+S4Z6T4Z674Z6E4Z6A4Z624Z6a4Z6U4Z6E4Z+S4Z6A4Z6+4Z6P4Z6Y4Z674Z6B4Z6c4Z634Z6H4Z+S4Z6H4Z62IScpO1xuICB9IGZpbmFsbHkge1xuICAgIGlzU3VibWl0dGluZy52YWx1ZSA9IGZhbHNlO1xuICB9XG59O1xuXG5jb25zdCBzdWJtaXRVcGRhdGVTdWJqZWN0ID0gYXN5bmMgKCkgPT4ge1xuICBpZiAoIXNlbGVjdGVkU3ViamVjdC52YWx1ZSkgcmV0dXJuO1xuICBpc1N1Ym1pdHRpbmcudmFsdWUgPSB0cnVlO1xuICB0cnkge1xuICAgIGNvbnN0IHJlcyA9IGF3YWl0IGF4aW9zLnB1dChgL2FwaS9jb3Vyc2Utc3ViamVjdHMvJHtzZWxlY3RlZFN1YmplY3QudmFsdWUuaWR9YCwgZWRpdEZvcm0udmFsdWUpO1xuICAgIHNob3dFZGl0TW9kYWwudmFsdWUgPSBmYWxzZTtcbiAgICBhbGVydChyZXMuZGF0YS5tZXNzYWdlIHx8ICfhnpThnrbhnpPhnoDhn4LhnpThn5Lhnprhn4Lhnpbhn5Dhno/hn4zhnpjhnrbhnpPhnpjhnrvhnoHhnpzhnrfhnofhn5Lhnofhnrbhnofhn4ThnoLhnofhn5DhnpkhJyk7XG4gICAgYXdhaXQgZmV0Y2hTdWJqZWN0cygpO1xuICB9IGNhdGNoIChlcnIpIHtcbiAgICBhbGVydChlcnIucmVzcG9uc2U/LmRhdGE/Lm1lc3NhZ2UgfHwgJ+GemOGetuGek+GelOGeieGfkuGeoOGetuGegOGfkuGek+Geu+GehOGegOGetuGemuGegOGfguGelOGfkuGemuGfguGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetiEnKTtcbiAgfSBmaW5hbGx5IHtcbiAgICBpc1N1Ym1pdHRpbmcudmFsdWUgPSBmYWxzZTtcbiAgfVxufTtcblxuY29uc3QgaGFuZGxlRGVsZXRlID0gYXN5bmMgKHMpID0+IHtcbiAgaWYgKCFjb25maXJtKGDhno/hnr7hnqLhn5LhnpPhnoDhnpbhnrfhno/hnofhnrbhnoXhnoThn4vhnpvhnrvhnpThnpjhnrvhnoHhnpzhnrfhnofhn5LhnofhnrYgXCIke3MubmFtZUtoIHx8IHMubmFtZX1cIiDhnpPhn4Hhn4fhnpjhn4LhnpPhnpHhn4E/YCkpIHJldHVybjtcbiAgdHJ5IHtcbiAgICBjb25zdCByZXMgPSBhd2FpdCBheGlvcy5kZWxldGUoYC9hcGkvY291cnNlLXN1YmplY3RzLyR7cy5pZH1gKTtcbiAgICBhbGVydChyZXMuZGF0YS5tZXNzYWdlIHx8ICfhnpThnrbhnpPhnpvhnrvhnpThnpjhnrvhnoHhnpzhnrfhnofhn5Lhnofhnrbhnorhn4Thnpnhnofhn4ThnoLhnofhn5DhnpkhJyk7XG4gICAgYXdhaXQgZmV0Y2hTdWJqZWN0cygpO1xuICB9IGNhdGNoIChlcnIpIHtcbiAgICBhbGVydChlcnIucmVzcG9uc2U/LmRhdGE/Lm1lc3NhZ2UgfHwgJ+GemOGet+Gek+GeouGetuGeheGem+Geu+GelOGemOGeu+GegeGenOGet+Geh+GfkuGeh+GetuGek+GfgeGfh+GelOGetuGek+GekeGfgSEnKTtcbiAgfVxufTtcblxuY29uc3QgaGFuZGxlUHJpbnQgPSAoKSA9PiB3aW5kb3cucHJpbnQoKTtcblxuY29uc3QgaGFuZGxlRXhwb3J0ID0gKCkgPT4ge1xuICBjb25zdCBjc3YgPSBbXG4gICAgWyfhnoDhnrzhnoonLCAn4Z6I4Z+S4Z6Y4Z+E4Z+H4Z6B4Z+S4Z6Y4Z+C4Z6aJywgJ+GeiOGfkuGemOGfhOGfh+GeouGehOGfi+GeguGfkuGem+GfgeGenycsICfhnpThn5Lhnprhnpfhn4HhnpHhnpjhnrvhnoHhnpzhnrfhnofhn5LhnofhnrYnLCAn4Z6Y4Z+J4Z+E4Z6E4Z6f4Z634Z6A4Z+S4Z6f4Z62L+Gen+GelOGfkuGej+GetuGeoOGfjScsICfhnoDhnpjhn5Lhnprhnrfhno/hnpDhn5LhnpPhnrbhnoDhn4snXS5qb2luKCcsJyksXG4gICAgLi4uc3ViamVjdHNMaXN0LnZhbHVlLm1hcChzID0+IFtcbiAgICAgIGBcIiR7cy5jb2RlfVwiYCxcbiAgICAgIGBcIiR7cy5uYW1lS2ggfHwgcy5uYW1lfVwiYCxcbiAgICAgIGBcIiR7cy5uYW1lRW4gfHwgcy5kZXNjcmlwdGlvbiB8fCAnJ31cImAsXG4gICAgICBgXCIke3MudHlwZUxhYmVsIHx8IHMudHlwZSB8fCAnJ31cImAsXG4gICAgICBgXCIke3MuY3JlZGl0cyB8fCBzLmNyZWRpdF9ob3Vyc31cImAsXG4gICAgICBgXCIke3MuZ3JhZGVfbGV2ZWwgfHwgJyd9XCJgXG4gICAgXS5qb2luKCcsJykpXG4gIF0uam9pbignXFxuJyk7XG5cbiAgY29uc3QgYmxvYiA9IG5ldyBCbG9iKFtcIlxcdUZFRkZcIiArIGNzdl0sIHsgdHlwZTogJ3RleHQvY3N2O2NoYXJzZXQ9dXRmLTg7JyB9KTtcbiAgY29uc3QgdXJsID0gVVJMLmNyZWF0ZU9iamVjdFVSTChibG9iKTtcbiAgY29uc3QgYSA9IGRvY3VtZW50LmNyZWF0ZUVsZW1lbnQoJ2EnKTtcbiAgYS5ocmVmID0gdXJsO1xuICBhLmRvd25sb2FkID0gYExNU1RJQ0tfU3ViamVjdHNfJHtuZXcgRGF0ZSgpLnRvSVNPU3RyaW5nKCkuc2xpY2UoMCwgMTApfS5jc3ZgO1xuICBhLmNsaWNrKCk7XG59O1xuPC9zY3JpcHQ+XG4iXSwibWFwcGluZ3MiOiJBQWlRQSxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzlDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbEMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN6QixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDckQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDN0MsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDekQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNqRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNuRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQzs7Ozs7Ozs7QUFFbkQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDOztBQUV2QixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzVCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzNCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7O0FBRTVCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbEMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2hDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2pDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7O0FBRS9CLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN2QixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ1YsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNWLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDdEIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2pCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNqQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNyQixDQUFDLENBQUM7O0FBRUYsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDckIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNWLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDVixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3RCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNqQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDakIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDckIsQ0FBQyxDQUFDOztBQUVGLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbEMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDTixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3ZELENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2hELENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDaEIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbkQsQ0FBQyxDQUFDO0FBQ0YsQ0FBQzs7QUFFRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2hCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNqQixDQUFDLENBQUM7O0FBRUYsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN4QyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3hDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNwSCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUM3QyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQy9CLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3pELENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNyRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN6RCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDdEMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3RDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDSixDQUFDLENBQUM7O0FBRUYsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7O0FBRTNILENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzlCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3JCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ1osQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDWixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN4QixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ25CLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbkIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3ZCLENBQUMsQ0FBQyxDQUFDO0FBQ0gsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDOUIsQ0FBQzs7QUFFRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDN0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDM0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNuQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2hCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDNUIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNwRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNoRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbEQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN4QyxDQUFDLENBQUMsQ0FBQztBQUNILENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDNUIsQ0FBQzs7QUFFRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3hDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzNCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ04sQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzFFLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2pDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUM5RCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3pCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDaEIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUM1RSxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNaLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzlCLENBQUMsQ0FBQztBQUNGLENBQUM7O0FBRUQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN4QyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNwQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUMzQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNOLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ25HLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDL0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2pFLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDekIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNoQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzVFLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ1osQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDOUIsQ0FBQyxDQUFDO0FBQ0YsQ0FBQzs7QUFFRCxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2xDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN0RixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNOLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2xFLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDMUQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN6QixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2hCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN2RSxDQUFDLENBQUM7QUFDRixDQUFDOztBQUVELENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7O0FBRXhDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQzNCLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUNkLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDN0csQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ25DLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDbkIsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUMvQixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDNUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQztBQUN4QyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ3hDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDL0IsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDZixDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7O0FBRWQsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDOUUsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDdkMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUM7QUFDdkMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDO0FBQ2QsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FBQyxDQUFDLENBQUMsQ0FB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
