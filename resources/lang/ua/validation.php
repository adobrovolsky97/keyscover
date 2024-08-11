<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => 'Ви повинні прийняти.',
    'active_url'           => 'Поле не є дійсною URL-адресою.',
    'after'                => 'Поле повинно бути датою після :date.',
    'after_or_equal'       => 'Поле повинно бути датою після або рівною :date.',
    'alpha'                => 'Поле може містити лише літери.',
    'alpha_dash'           => 'Поле може містити лише літери, цифри, дефіс і підкреслення.',
    'alpha_num'            => 'Поле може містити лише літери та цифри.',
    'array'                => 'Поле повинно бути масивом.',
    'before'               => 'Поле повинно бути датою перед :date.',
    'before_or_equal'      => 'Поле повинно бути датою перед або рівною :date.',
    'between' => [
        'numeric' => 'Поле повинно бути між :min і :max.',
        'file'    => 'Розмір файлу повинен бути від :min до :max кілобайт.',
        'string'  => 'Довжина повинна бути від :min до :max символів.',
        'array'   => 'Поле повинно містити від :min до :max елементів.',
    ],
    'boolean'              => 'Поле повинно бути булевим значенням.',
    'confirmed'            => 'Поле не співпадає з підтвердженням.',
    'date'                 => 'Поле не є дійсною датою.',
    'date_format'          => 'Поле не відповідає формату :format.',
    'different'            => 'Поля і :other повинні відрізнятися.',
    'digits'               => 'Довжина цифрового поля повинна бути :digits.',
    'digits_between'       => 'Довжина цифрового поля повинна бути між :min і :max.',
    'dimensions'           => 'Поле має недопустимі розміри зображення.',
    'distinct'             => 'Поле має повторюване значення.',
    'email'                => 'Поле повинно бути дійсною електронною адресою.',
    'exists'               => 'Вибране значення для є некоректним.',
    'file'                 => 'Поле повинно бути файлом.',
    'filled'               => 'Поле є обов\'язковим для заповнення.',
    'image'                => 'Поле повинно бути зображенням.',
    'in'                   => 'Вибране значення для є помилковим.',
    'in_array'             => 'Поле не існує в :other.',
    'integer'              => 'Поле повинно бути цілим числом.',
    'ip'                   => 'Поле повинно бути дійсною IP-адресою.',
    'ipv4'                 => 'Поле повинно бути дійсною IPv4-адресою.',
    'ipv6'                 => 'Поле повинно бути дійсною IPv6-адресою.',
    'json'                 => 'Поле повинно бути дійсною JSON-строкою.',
    'max'                  => [
        'numeric' => 'Поле повинно бути не більше :max.',
        'file'    => 'Поле повинно бути не більше :max кілобайт.',
        'string'  => 'Поле повинно бути не довше :max символів.',
        'array'   => 'Поле повинно містити не більше :max елементів.',
    ],
    'mimes'                => 'Поле повинно бути файлом одного з типів: :values.',
    'mimetypes'            => 'Поле повинно бути файлом одного з типів: :values.',
    'min'                  => [
        'numeric' => 'Поле повинно бути не менше :min.',
        'file'    => 'Поле повинно бути не менше :min кілобайт.',
        'string'  => 'Поле повинно бути не коротше :min символів.',
        'array'   => 'Поле повинно містити не менше :min елементів.'
    ],
    'not_in'               => 'Вибране значення для є помилковим.',
    'numeric'              => 'Поле повинно бути числом.',
    'present'              => 'Поле повинно бути присутнім.',
    'regex'                => 'Поле має помилковий формат.',
    'required'             => 'Поле є обов\'язковим для заповнення.',
    'required_if'          => 'Поле є обов\'язковим для заповнення',
    'required_unless'      => 'Поле є обов\'язковим для заповнення, коли :other не дорівнює :values.',
    'required_with'        => 'Поле є обов\'язковим для заповнення, коли :values вказано.',
    'required_with_all'    => 'Поле є обов\'язковим для заповнення, коли :values вказані.',
    'required_without'     => 'Поле є обов\'язковим для заповнення, коли :values не вказано.',
    'required_without_all' => 'Поле є обов\'язковим для заповнення, коли :values не вказані.',
    'same'                 => 'Значення повинно співпадати з :other.',
    'size'                 => [
        'numeric' => 'Поле повинно бути :size.',
        'file'    => 'Поле повинно бути :size кілобайт.',
        'string'  => 'Поле повинно бути довжиною :size символів.',
        'array'   => 'Кількість елементів в полі повинна бути :size.'
    ],
    'string'               => 'Поле повинно бути строкою.',
    'timezone'             => 'Поле повинно бути дійсною часовою зоною.',
    'unique'               => 'Таке значення поля вже існує.',
    'uploaded'             => 'Завантаження поля не вдалося.',
    'url'                  => 'Поле має помилковий формат.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap attribute place-holders
    | with something more reader friendly such as E-Mail Address instead
    | of "email". This simply helps us make messages a little cleaner.
    |
    */

    'attributes' => [],

];
