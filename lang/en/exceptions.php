<?php
// TODO if we ever need english trans, this file is missing some keys

use App\Exceptions\Base\ExceptionCodes;

return [
    ExceptionCodes::REPOSITORY_DELETE_MODEL => 'مدل :model :id را نمی توان حذف نمود.',
    ExceptionCodes::REPOSITORY_MODEL_NOT_FOUND => 'مدل مورد نظر یافت نشد.',
    ExceptionCodes::LOCKED_INVOICE_ALREADY_IMPORTED_TO_RAHKARAN => 'فاکتور :invoice_id قابل ویرایش نیست.',
    ExceptionCodes::MAIN_APP_INTERNAL_API => 'MainApp internal api failed :url :param',
];
