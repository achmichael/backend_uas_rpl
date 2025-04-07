<?php

function success($data, $message = 'Success', $code = 200)
{
    return response()->json([
        'success' => true,
        'message' => $message,
        'data'    => $data,
    ], $code);
}

function error($message = 'Error', $code = 400)
{
    return response()->json([
        'success' => false,
        'message' => $message,
    ], $code);
}

function msg($success = false, $message = 'Success', $code = 200)
{
    return response()->json([
        'success' => $success,
        'message' => $message,
    ], $code);
}
