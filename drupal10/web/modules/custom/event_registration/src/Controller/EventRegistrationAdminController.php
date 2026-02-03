return [
  '#type' => 'container',
  '#attributes' => ['class' => ['event-registration-admin']],
  'card' => [
    '#type' => 'container',
    '#attributes' => ['class' => ['event-registration-card']],
    'table' => $table, // your existing table render array
  ],
  '#attached' => [
    'library' => [
      'event_registration/admin',
    ],
  ],
];
