<?php

return [
    'required' => 'حقل :attribute مطلوب.',
    'string' => 'يجب أن يكون :attribute نصًا.',
    'email' => 'يرجى إدخال :attribute صالح.',
    // "عدد أحرف" keeps the sentence correct for any number (no number–noun agreement needed).
    'min' => [
        'string' => 'يجب ألا يقل عدد أحرف :attribute عن :min.',
    ],
    'max' => [
        'string' => 'يجب ألا يتجاوز عدد أحرف :attribute :max.',
        'file' => 'يجب ألا يتجاوز حجم :attribute :max كيلوبايت.',
    ],
    'unique' => ':attribute مستخدم مسبقًا.',
    'regex' => 'صيغة :attribute غير صحيحة.',
    'image' => 'يجب أن يكون :attribute صورة.',
    'mimes' => 'يجب أن يكون نوع ملف :attribute من الأنواع التالية: :values.',
    'uploaded' => 'تعذّر رفع :attribute، حاول مرة أخرى.',
    'in' => 'القيمة المختارة في :attribute غير صالحة.',
    'numeric' => 'يجب أن يكون :attribute رقمًا.',
    'confirmed' => 'تأكيد :attribute غير مطابق.',
    'attributes' => [
        'name' => 'الاسم',
        'username' => 'اسم المستخدم',
        'email' => 'البريد الإلكتروني',
        'password' => 'كلمة المرور',
        'profile_photo' => 'الصورة الشخصية',
        'content' => 'نص الاقتباس',
        'author' => 'صاحب الاقتباس',
        'color' => 'لون البطاقة',
    ],
];
