<?php

namespace Celovel\Http;

use Celovel\Http\Request;
use Celovel\Http\Response;
use Celovel\View\BladeView;

abstract class Controller
{
    protected Request $request;

    public function __construct(?Request $request = null)
    {
        $this->request = $request ?? Request::createFromGlobals();
    }

    protected function view(string $view, array $data = []): Response
    {
        $viewService = app('view');
        $content = $viewService->render($view, $data);
        return Response::make($content);
    }

    protected function json(array $data, int $statusCode = 200): Response
    {
        return (new Response())->json($data, $statusCode);
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return (new Response())->redirect($url, $statusCode);
    }

    protected function response(string $content = '', int $statusCode = 200, array $headers = []): Response
    {
        return Response::make($content, $statusCode, $headers);
    }

    protected function validate(array $rules): array
    {
        $data = $this->request->input();
        $errors = [];

        foreach ($rules as $field => $rule) {
            $value = $data[$field] ?? null;
            
            if (is_string($rule)) {
                $ruleArray = explode('|', $rule);
            } else {
                $ruleArray = $rule;
            }

            foreach ($ruleArray as $singleRule) {
                $error = $this->validateField($field, $value, $singleRule);
                if ($error) {
                    $errors[$field][] = $error;
                }
            }
        }

        return $errors;
    }

    protected function validateField(string $field, $value, string $rule): ?string
    {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleValue = $ruleParts[1] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value)) {
                    return "The {$field} field is required.";
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    return "The {$field} field must be a valid email address.";
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleValue) {
                    return "The {$field} field must be at least {$ruleValue} characters.";
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleValue) {
                    return "The {$field} field must not exceed {$ruleValue} characters.";
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    return "The {$field} field must be numeric.";
                }
                break;

            case 'integer':
                if (!empty($value) && !is_int($value) && !ctype_digit($value)) {
                    return "The {$field} field must be an integer.";
                }
                break;
        }

        return null;
    }

    protected function getRequest(): Request
    {
        return $this->request;
    }
}
