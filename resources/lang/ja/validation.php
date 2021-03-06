<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages.
    |
     */

    'accepted' => ':attributeを承認してください。',
    'active_url' => ':attributeは、有効なURLではありません。',
    'after' => ':attributeには、:date以降の日付を指定してください。',
    'after_or_equal' => ':attributeには、:date以降もしくは同日時を指定してください。',
    'alpha' => ':attributeには、アルファベッドのみ使用できます。',
    'alpha_dash' => ":attributeには、英数字('A-Z','a-z','0-9')とハイフンと下線('-','_')が使用できます。",
    'alpha_num' => ":attributeには、英数字('A-Z','a-z','0-9')が使用できます。",
    'array' => ':attributeには、配列を指定してください。',
    'before' => ':attributeには、:date以前の日付を指定してください。',
    'before_or_equal' => ':attributeには、:date以前もしくは同日時を指定してください。',
    'between' => [
        'numeric' => ':attributeには、:minから、:maxまでの数字を指定してください。',
        'file' => ':attributeには、:min KBから:max KBまでのサイズのファイルを指定してください。',
        'string' => ':attributeは、:min文字から:max文字にしてください。',
        'array' => ':attributeの項目は、:min個から:max個にしてください。',
    ],
    'boolean' => ":attributeには、'true'か'false'を指定してください。",
    'confirmed' => ':attributeと:attribute確認が一致しません。',
    'date' => ':attributeは、正しい日付ではありません。',
    'date_format' => ":attributeの形式は、':format'と合いません。",
    'different' => ':attributeと:otherには、異なるものを指定してください。',
    'digits' => ':attributeは、:digits桁にしてください。',
    'digits_between' => ':attributeは、:min桁から:max桁にしてください。',

    'dimensions' => ':attributeは、正しい縦横比ではありません。',
    'distinct' => ':attributeに重複した値があります。',
    'email' => ':attributeは、有効なメールアドレス形式で指定してください。',
    'exists' => '選択された:attributeは、有効ではありません。',
    'file' => ':attributeはファイルでなければいけません。',
    'filled' => ':attributeは必須です。',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.',
        'file' => 'The :attribute must be greater than :value kilobytes.',
        'string' => 'The :attribute must be greater than :value characters.',
        'array' => 'The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal :value.',
        'file' => 'The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'The :attribute must be greater than or equal :value characters.',
        'array' => 'The :attribute must have :value items or more.',
    ],
    'image' => ':attributeには、画像を指定してください。',
    'in' => '選択された:attributeは、有効ではありません。',
    'in_array' => ':attributeは、:otherに存在しません。',
    'integer' => ':attributeには、整数を指定してください。',
    'ip' => ':attributeには、有効なIPアドレスを指定してください。',
    'ipv4' => ':attributeはIPv4アドレスを指定してください。',
    'ipv6' => ':attributeはIPv6アドレスを指定してください。',
    'json' => ':attributeには、有効なJSON文字列を指定してください。',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.',
        'file' => 'The :attribute must be less than :value kilobytes.',
        'string' => 'The :attribute must be less than :value characters.',
        'array' => 'The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal :value.',
        'file' => 'The :attribute must be less than or equal :value kilobytes.',
        'string' => 'The :attribute must be less than or equal :value characters.',
        'array' => 'The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => ':attributeには、:max以下の数字を指定してください。',
        'file' => ':attributeには、:max KB以下のファイルを指定してください。',
        'string' => ':attributeは、:max文字以下にしてください。',
        'array' => ':attributeの項目は、:max個以下にしてください。',
    ],
    'mimes' => ':attributeには、:valuesタイプのファイルを指定してください。',
    'mimetypes' => ':attributeには、:valuesタイプのファイルを指定してください。',
    'min' => [
        'numeric' => ':attributeには、:min以上の数字を指定してください。',
        'file' => ':attributeには、:min KB以上のファイルを指定してください。',
        'string' => ':attributeは、:min文字以上にしてください。',
        'array' => ':attributeの項目は、:max個以上にしてください。',
    ],
    'not_in' => '選択された:attributeは、有効ではありません。',
    'not_regex' => 'The :attribute format is invalid.',
    'numeric' => ':attributeには、数字を指定してください。',
    'present' => ':attributeは、必ず存在しなくてはいけません。',
    'regex' => ':attributeには、有効な正規表現を指定してください。',
    'required' => ':attributeは、必ず指定してください。',
    'required_if' => ':otherが:valueの場合、:attributeを指定してください。',
    'required_unless' => ':otherが:value以外の場合、:attributeを指定してください。',
    'required_with' => ':valuesが指定されている場合、:attributeも指定してください。',
    'required_with_all' => ':valuesが全て指定されている場合、:attributeも指定してください。',
    'required_without' => ':valuesが指定されていない場合、:attributeを指定してください。',
    'required_without_all' => ':valuesが全て指定されていない場合、:attributeを指定してください。',
    'same' => ':attributeと:otherが一致しません。',
    'size' => [
        'numeric' => ':attributeには、:sizeを指定してください。',
        'file' => ':attributeには、:size KBのファイルを指定してください。',
        'string' => ':attributeは、:size文字にしてください。',
        'array' => ':attributeの項目は、:size個にしてください。',
    ],
    'string' => ':attributeには、文字を指定してください。',
    'timezone' => ':attributeには、有効なタイムゾーンを指定してください。',
    'unique' => '指定の:attributeは既に使用されています。',
    'uploaded' => ':attributeのアップロードに失敗しました。',
    'url' => ':attributeは、有効なURL形式で指定してください。',

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

    'phone' => '正しい電話番号を入力してください',

    'custom' => [
        'nickname' => [
            'max' => '20文字以内で入力してください',
        ],
        'intro' => [
            'max' => '30文字以内で入力してください',
        ],
        'description' => [
            'max' => '1000文字以内で入力してください',
        ],
        'front_side' => [
            'required' => 'おもて面は、必ず指定してください。',
        ],
        'back_side' => [
            'required' => 'うら面は、必ず指定してください。',
        ],
        'satisfaction' => [
            'required_without' => '満足度を選択してください',
        ],
        'appearance' => [
            'required_without' => 'ルックス・身だしなみを選択してください',
        ],
        'friendliness' => [
            'required_without' => '愛想・気遣いを選択してください',
        ],
        'comment' => [
            'required_without' => 'コメントを記入してください',
        ],
        'number' => [
            'numeric' => '口座番号には、数字を指定してください。',
        ],
        'number' => [
            'digits' => '口座番号は7桁で入力してください',
        ],
        'point' => [
            'regex' => 'ポイントは半角数字で入力してください',
        ],
        'post_code' => [
            'required' => '郵便番号を設定してください',
        ],
        'lastname_kana' => [
            'regex' => 'お名前（ふりがな）はひらがなとスペースを利用してください',
        ],
        'firstname_kana' => [
            'regex' => 'お名前（ふりがな）はひらがなとスペースを利用してください',
        ],
        'to_date' => [
            'after_or_equal' => '次回クラス変更期間のto dataは、from data以降の時間を指定してください',
        ],
        'num_of_attend_platium' => [
            'required' => 'プラチナまでの参加回数は必須項目です',
            'numeric' => 'プラチナまでの参加回数は数字で入力してください',
        ],
        'num_of_avg_rate_platium' => [
            'required' => 'プラチナまでの平均評価は必須項目です',
            'numeric' => 'プラチナまでの平均評価は数字で入力してください',
        ],
        'num_of_attend_up_platium' => [
            'required' => 'プラチナキープの参加回数は必須項目です',
            'numeric' => 'プラチナキープの参加回数は数字で入力してください',
        ],
        'num_of_avg_rate_up_platium' => [
            'required' => 'プラチナキープの平均評価は必須項目です',
            'numeric' => 'プラチナキープの平均評価は数字で入力してください',
        ],
        'fullname_kana' => [
            'regex' => '"お名前(ふりがな)"は必ず入力してください',
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

    'attributes' => [
        'nickname' => 'ニックネーム',
        'gender' => '性別',
        'date_of_birth' => '生年月日',
        'phone' => '電話番号',
        'height' => '身長',
        'body_type_id' => '体型',
        'prefecture_id' => '居住地',
        'hometown_id' => '出身地',
        'job_id' => 'お仕事',
        'drink_volume_type' => 'お酒',
        'smoking_type' => 'タバコ',
        'cohabitant_type' => '同居人',
        'intro' => 'ひとこと',
        'description' => '自己紹介',
        'type' => '口座種別',
        'bank_name' => '銀行名',
        'branch_name' => '支店名',
        'holder_name' => '口座名義',
        'send_date' => '投稿日時',
        'title' => 'タイトル',
        'content' => '内容',
        'status' => '状況',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'lastname' => '姓',
        'firstname' => '名',
        'lastname_kana' => 'せい',
        'firstname_kana' => 'めい',
        'account_number' => '口座番号',
        'memo' => '運営者メモ',
    ],
];
