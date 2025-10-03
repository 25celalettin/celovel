<?php

namespace Celovel\View;

class BladeCompiler
{
    protected string $viewsPath;
    protected string $cachePath;
    protected array $directives = [];
    protected array $compilers = [];

    public function __construct(string $viewsPath, string $cachePath)
    {
        $this->viewsPath = $viewsPath;
        $this->cachePath = $cachePath;
        
        $this->registerDefaultDirectives();
        $this->registerDefaultCompilers();
    }

    protected function registerDefaultDirectives(): void
    {
        $this->directives = [
            'extends' => 'handleExtends',
            'section' => 'handleSection',
            'endsection' => 'handleEndSection',
            'yield' => 'handleYield',
            'include' => 'handleInclude',
            'if' => 'handleIf',
            'elseif' => 'handleElseIf',
            'else' => 'handleElse',
            'endif' => 'handleEndIf',
            'foreach' => 'handleForeach',
            'endforeach' => 'handleEndForeach',
            'for' => 'handleFor',
            'endfor' => 'handleEndFor',
            'while' => 'handleWhile',
            'endwhile' => 'handleEndWhile',
            'unless' => 'handleUnless',
            'endunless' => 'handleEndUnless',
            'isset' => 'handleIsset',
            'endisset' => 'handleEndIsset',
            'empty' => 'handleEmpty',
            'endempty' => 'handleEndEmpty',
        ];
    }

    protected function registerDefaultCompilers(): void
    {
        $this->compilers = [
            'Comments',
            'Echos',
            'EscapedEchos',
            'Directives',
            'Php',
        ];
    }

    public function compile(string $view): string
    {
        $viewPath = $this->getViewPath($view);
        
        if (!file_exists($viewPath)) {
            throw new \Exception("View [{$view}] not found.");
        }

        $content = file_get_contents($viewPath);
        $compiledPath = $this->getCompiledPath($view);

        // Cache kontrolü
        if ($this->isExpired($viewPath, $compiledPath)) {
            $compiled = $this->compileString($content);
            $this->ensureCompiledDirectoryExists($compiledPath);
            file_put_contents($compiledPath, $compiled);
        }

        return $compiledPath;
    }

    public function compileString(string $content): string
    {
        foreach ($this->compilers as $compiler) {
            $method = "compile{$compiler}";
            if (method_exists($this, $method)) {
                $content = $this->$method($content);
            }
        }

        return $content;
    }

    protected function compileComments(string $content): string
    {
        return preg_replace('/{{--(.*?)--}}/s', '<?php /*$1*/ ?>', $content);
    }

    protected function compileEchos(string $content): string
    {
        return preg_replace('/\{\{\s*(.+?)\s*\}\}/', '<?php echo $1; ?>', $content);
    }

    protected function compileEscapedEchos(string $content): string
    {
        return preg_replace('/\{\{\{\s*(.+?)\s*\}\}\}/', '<?php echo htmlspecialchars($1, ENT_QUOTES, \'UTF-8\'); ?>', $content);
    }

    protected function compileDirectives(string $content): string
    {
        // Önce @if, @foreach gibi karmaşık directive'leri işle
        $content = $this->compileComplexDirectives($content);
        
        // Sonra basit directive'leri işle
        foreach ($this->directives as $directive => $handler) {
            if (in_array($directive, ['if', 'foreach', 'for', 'while', 'unless', 'isset', 'empty'])) {
                continue; // Bunları zaten yukarıda işledik
            }
            
            $pattern = '/@' . $directive . '\s*(\([^)]*\))?/';
            $content = preg_replace_callback($pattern, function ($matches) use ($handler) {
                $args = isset($matches[1]) ? trim($matches[1], '()') : '';
                return $this->$handler($args);
            }, $content);
        }

        return $content;
    }

    protected function compileComplexDirectives(string $content): string
    {
        // @if directive - iç içe parantezleri handle eden regex
        $content = $this->compileIfDirectives($content);
        $content = preg_replace('/@else/', '<?php else: ?>', $content);
        $content = preg_replace('/@endif/', '<?php endif; ?>', $content);
        
        // @foreach directive
        $content = preg_replace('/@foreach\s*\(([^)]+)\)/', '<?php foreach($1): ?>', $content);
        $content = preg_replace('/@endforeach/', '<?php endforeach; ?>', $content);
        
        // @for directive
        $content = preg_replace('/@for\s*\(([^)]+)\)/', '<?php for($1): ?>', $content);
        $content = preg_replace('/@endfor/', '<?php endfor; ?>', $content);
        
        // @while directive
        $content = preg_replace('/@while\s*\(([^)]+)\)/', '<?php while($1): ?>', $content);
        $content = preg_replace('/@endwhile/', '<?php endwhile; ?>', $content);
        
        // @unless directive
        $content = preg_replace('/@unless\s*\(([^)]+)\)/', '<?php if(!($1)): ?>', $content);
        $content = preg_replace('/@endunless/', '<?php endif; ?>', $content);
        
        // @isset directive
        $content = preg_replace('/@isset\s*\(([^)]+)\)/', '<?php if(isset($1)): ?>', $content);
        $content = preg_replace('/@endisset/', '<?php endif; ?>', $content);
        
        // @empty directive
        $content = preg_replace('/@empty\s*\(([^)]+)\)/', '<?php if(empty($1)): ?>', $content);
        $content = preg_replace('/@endempty/', '<?php endif; ?>', $content);

        return $content;
    }

    protected function compileIfDirectives(string $content): string
    {
        // @if directive - iç içe parantezleri handle eden regex
        $content = preg_replace_callback('/@if\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/', function($matches) {
            return '<?php if(' . $matches[1] . '): ?>';
        }, $content);
        
        // @elseif directive
        $content = preg_replace_callback('/@elseif\s*\(([^()]*(?:\([^()]*\)[^()]*)*)\)/', function($matches) {
            return '<?php elseif(' . $matches[1] . '): ?>';
        }, $content);
        
        return $content;
    }

    protected function compilePhp(string $content): string
    {
        return preg_replace('/@php(.*?)@endphp/s', '<?php$1?>', $content);
    }

    // Directive Handlers
    protected function handleExtends(string $args): string
    {
        $view = trim($args, '\'"');
        return "<?php \$this->layout = '{$view}'; ?>";
    }

    protected function handleSection(string $args): string
    {
        $name = trim($args, '\'"');
        return "<?php \$this->startSection('{$name}'); ?>";
    }

    protected function handleEndSection(): string
    {
        return "<?php \$this->endSection(); ?>";
    }

    protected function handleYield(string $args): string
    {
        $name = trim($args, '\'"');
        return "<?php echo \$this->yieldSection('{$name}'); ?>";
    }

    protected function handleInclude(string $args): string
    {
        $view = trim($args, '\'"');
        return "<?php echo \$this->includeView('{$view}'); ?>";
    }

    protected function handleIf(string $args): string
    {
        return "<?php if({$args}): ?>";
    }

    protected function handleElseIf(string $args): string
    {
        return "<?php elseif({$args}): ?>";
    }

    protected function handleElse(): string
    {
        return "<?php else: ?>";
    }

    protected function handleEndIf(): string
    {
        return "<?php endif; ?>";
    }

    protected function handleForeach(string $args): string
    {
        return "<?php foreach({$args}): ?>";
    }

    protected function handleEndForeach(): string
    {
        return "<?php endforeach; ?>";
    }

    protected function handleFor(string $args): string
    {
        return "<?php for({$args}): ?>";
    }

    protected function handleEndFor(): string
    {
        return "<?php endfor; ?>";
    }

    protected function handleWhile(string $args): string
    {
        return "<?php while({$args}): ?>";
    }

    protected function handleEndWhile(): string
    {
        return "<?php endwhile; ?>";
    }

    protected function handleUnless(string $args): string
    {
        return "<?php if(!({$args})): ?>";
    }

    protected function handleEndUnless(): string
    {
        return "<?php endif; ?>";
    }

    protected function handleIsset(string $args): string
    {
        return "<?php if(isset({$args})): ?>";
    }

    protected function handleEndIsset(): string
    {
        return "<?php endif; ?>";
    }

    protected function handleEmpty(string $args): string
    {
        return "<?php if(empty({$args})): ?>";
    }

    protected function handleEndEmpty(): string
    {
        return "<?php endif; ?>";
    }

    protected function getViewPath(string $view): string
    {
        $view = str_replace('.', '/', $view);
        return $this->viewsPath . '/' . $view . '.blade.php';
    }

    protected function getCompiledPath(string $view): string
    {
        $hash = md5($view);
        return $this->cachePath . '/' . $hash . '.php';
    }

    protected function isExpired(string $viewPath, string $compiledPath): bool
    {
        if (!file_exists($compiledPath)) {
            return true;
        }

        return filemtime($viewPath) > filemtime($compiledPath);
    }

    protected function ensureCompiledDirectoryExists(string $path): void
    {
        $directory = dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
    }

    public function clearCache(): void
    {
        $files = glob($this->cachePath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }
}
