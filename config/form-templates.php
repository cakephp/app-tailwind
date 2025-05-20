<?php
declare(strict_types=1);

/**
 * Custom templates for pagination elements.
 */
return [
    'button' => '<button class="bg-red-800 p-2 text-white rounded-sm"{{attrs}}>{{text}}</button>',
    'input' => '<input class="border border-slate-500 rounded-sm" type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputContainer' => '<div class="py-3 flex gap-2 justify-between">{{content}}</div>',
    'textarea' => '<textarea class="border border-slate-500 rounded-sm" name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'select' => '<select class="border border-slate-500 rounded-sm" name="{{name}}"{{attrs}}>{{content}}</select>',
];
