<?php
declare(strict_types=1);

/**
 * Custom templates for pagination elements.
 */
return [
    'button' => '<button class="bg-red-800 p-2 text-white rounded-sm"{{attrs}}>{{text}}</button>',
    'checkbox' => '<input class="mr-2" type="checkbox" name="{{name}}" value="{{value}}"{{attrs}}>',
    'input' => '<input class="border border-slate-500 rounded-sm max-w-md min-w-4" type="{{type}}" name="{{name}}"{{attrs}}>',
    'inputContainer' => '<div class="py-3 flex gap-2 justify-between">{{content}}</div>',
    'label' => '<label class="mr-2" {{attrs}}>{{text}}</label>',
    'textarea' => '<textarea class="border border-slate-500 rounded-sm max-w-md min-w-4" name="{{name}}"{{attrs}}>{{value}}</textarea>',
    'select' => '<select class="border border-slate-500 rounded-sm max-w-md min-w-4" name="{{name}}"{{attrs}}>{{content}}</select>',
    'selectMultiple' => '<select class="border border-slate-500 rounded-sm max-w-md min-w-50" name="{{name}}[]" multiple="multiple"{{attrs}}>{{content}}</select>',
];
