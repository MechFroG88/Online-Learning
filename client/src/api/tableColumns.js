let user = [
  { field: 'username', cn_label: '用户名', en_label: 'username', search: true },
  { field: 'cn_name', cn_label: '中文姓名', en_label: 'chinese name', search: true },
  { field: 'en_name', cn_label: '英文姓名', en_label: 'english name', search: true },
  { field: 'type', cn_label: '用户类型', en_label: 'user type' },
  { field: 'class_subjects', cn_label: '任教班级-科目', en_label: 'class and subject(s) in charge' },
  { field: 'action', cn_label: '操作', en_label: 'action' },
  { field: 'choice', cn_label: '选择', en_label: 'choose period' }
];

let event = [
  { field: 'start_date', cn_label: '开始日期', en_label: 'start date' },
  { field: 'end_date', cn_label: '结束日期', en_label: 'end date' },
  { field: 'start_pick_datetime', cn_label: '开始选课日期', en_label: 'selection start time' },
  { field: 'end_pick_datetime', cn_label: '结束选课日期', en_label: 'selection end time' },
  { field: 'action', cn_label: '操作', en_label: 'action' },
  { field: 'export', cn_label: '输出资料', en_label: 'export data' }
];

let period = [
  { field: 'start_time', cn_label: '开始时间', en_label: 'period start time' },
  { field: 'end_time', cn_label: '结束时间', en_label: 'period end time' },
  { field: 'action', cn_label: '操作', en_label: 'action' }
];

let classes = [
  { field: 'cn_name', cn_label: '中文名称', en_label: 'chinese name', search: true },
  { field: 'en_name', cn_label: '英文名称', en_label: 'english name', search: true },
  { field: 'action', cn_label: '操作', en_label: 'action' }
];

let subject = [
  { field: 'cn_name', cn_label: '中文名称', en_label: 'chinese name', search: true },
  { field: 'en_name', cn_label: '英文名称', en_label: 'english name', search: true },
  { field: 'day', cn_label: '每日上课限额', en_label: 'daily limit of periods' },
  { field: 'week', cn_label: '每周上课限额', en_label: 'weekly limit of periods' },
  { field: 'action', cn_label: '操作', en_label: 'action' }
];

let day = [
  { field: 'id', cn_label: '天', en_label: 'day' },
  { field: 'count', cn_label: '节数', en_label: 'no. of count' },
  { field: 'action', cn_label: '操作', en_label: 'action' }
]

module.exports = {
  user, event, period, classes, subject, day
}