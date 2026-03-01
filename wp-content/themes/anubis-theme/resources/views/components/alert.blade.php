@props([
  'type' => null,
  'message' => null,
])

@php($class = match ($type) {
  'restricted' => 'alert-restricted',
  'good' => 'alert-good',
  'warning' => 'alert-warning',
  'default' => ''
})

<div {{ $attributes->merge(['class' => "alert {$class}"]) }}>
  {!! $message ?? $slot !!}
</div>
