<?php
// app/Core/DuplicateRecordException.php
 
/**
 * Ném ra khi MySQL báo lỗi 1062 (Duplicate entry for UNIQUE key).
 * Dùng để phân biệt lỗi trùng dữ liệu với lỗi DB khác trong Controller/Service.
 */
class DuplicateRecordException extends RuntimeException {}