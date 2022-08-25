<?php

class TextAnalyzer
{
    /**
     * @var int
     */
    protected int $quantityPopularWords;

    /**
     * @var string
     */
    protected string $text;

    /**
     * @var array
     */
    protected array $groupedArrayByCountWords;

    /**
     * @var array
     */
    protected array $groupedArrayByWords;

    /**
     * @var int
     */
    protected int $countWordsInText;

    /**
     * @var array
     */
    protected array $storage;

    /**
     * @param string $text
     * @param int $quantityPopularWords
     */
    public function __construct(string $text, int $quantityPopularWords = 5)
    {
        $this->text = $text;
        $this->quantityPopularWords = $quantityPopularWords;
        $this->groupedArrayByCountWords = [];
        $this->groupedArrayByWords = [];
        $this->countWordsInText = 0;
        $this->storage = [];
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText(string $text): void
    {
        $this->clearPropertiesWithoutText();
        $this->text = $text;
    }

    /**
     * @return int
     */
    public function getQuantityPopularWords(): int
    {
        return $this->quantityPopularWords;
    }

    /**
     * @param int $quantityPopularWords
     */
    public function setQuantityPopularWords(int $quantityPopularWords): void
    {
        $this->clearPropertiesWithoutText();
        $this->quantityPopularWords = $quantityPopularWords;
    }

    /**
     * @return void
     */
    protected function clearPropertiesWithoutText(): void
    {
        $properties = get_class_vars(self::class);
        foreach ($properties as $property => $value) {
            if ($this->text === $this->$property) {
                continue;
            }
            switch (gettype($this->$property)) {
                case 'integer':
                    $this->$property = 0;
                    break;
                case 'string':
                    $this->$property = '';
                    break;
                case 'array':
                    $this->$property = [];
                    break;
            }
        }
    }

    /**
     * @return array
     */
    public function getGroupedArrayByCountWords(): array
    {
        if (count($this->groupedArrayByCountWords)) {
            return $this->groupedArrayByCountWords;
        }

        $groupedArrayByCountWords = [];
        $groupedArrayByWords = $this->getGroupedArrayByWords();
        foreach ($groupedArrayByWords as $word => $count) {
            $groupedArrayByCountWords[$count][$word] = $count;
        }

        return $this->groupedArrayByCountWords = $groupedArrayByCountWords;
    }

    /**
     * @return array
     */
    public function getGroupedArrayByWords(): array
    {
        if (count($this->groupedArrayByWords)) {
            return $this->groupedArrayByWords;
        }

        $arrayWord = [];
        $text = explode(' ', trim($this->getText()));
        foreach ($text as $word) {
            if ($word == '') {
                continue;
            }
            $arrayWord [$word] += 1;
        }

        return $this->groupedArrayByWords = $arrayWord;
    }

    /**
     * @return array
     */
    public function getUniqueOrPopularWords(): array
    {
        if ($this->getCountWordInText() >= $this->quantityPopularWords) {
            return $this->getUniqueWords();
        }

        return $this->getPopularWord();
    }

    /**
     * @return array
     */
    public function getUniqueWords(): array
    {
        if (isset($this->storage['unique_words'])) {
            return $this->storage['unique_words'];
        }

        return $this->storage['unique_words'] = $this->getGroupedArrayByCountWords()[1] ?? [];
    }

    /**
     * @return array
     */
    public function getPopularWord(): array
    {
        if (isset($this->storage['popular_word'])) {
            return $this->storage['popular_word'];
        }
        $groupedArrayByCountWords = $this->getGroupedArrayByCountWords();
        krsort($groupedArrayByCountWords, SORT_NUMERIC);

        $result = [];
        foreach ($groupedArrayByCountWords as $groupedArrayByWords) {
            $countResult = count($result);
            krsort($groupedArrayByWords);
            if ($countResult < $this->quantityPopularWords) {
                $sliceLength = $this->quantityPopularWords - $countResult;
                $result = array_merge($result, array_slice($groupedArrayByWords, 0, $sliceLength));
                continue;
            }
            break;
        }

        return $this->storage['popular_word'] = $result;
    }

    /**
     * @return int
     */
    public function getCountWordInText(): int
    {
        if ($this->countWordsInText) {
            return $this->countWordsInText;
        }

        $this->countWordsInText = 0;
        $groupedArrayByWords = $this->getGroupedArrayByWords();
        foreach ($groupedArrayByWords as $word => $countWord) {
            $this->countWordsInText += $countWord;
        }

        return $this->countWordsInText;
    }

    /**
     * @return string
     */
    public function getCountCharactersInText(): string
    {
        if (isset($this->storage['count_characters_in_text'])) {
            return $this->storage['count_characters_in_text'];
        }

        return $this->storage['count_characters_in_text'] = strlen($this->getText());
    }

    /**
     * @return string
     */
    public function getCountCharactersWithoutSpaceInText(): string
    {
        if (isset($this->storage['count_characters_without_space_in_text'])) {
            return $this->storage['count_characters_without_space_in_text'];
        }

        $spaceCount = substr_count($this->getText(), ' ');
        $countCharactersInText = $this->getCountCharactersInText();

        return $this->storage['count_characters_without_space_in_text'] = $countCharactersInText - $spaceCount;
    }
}
