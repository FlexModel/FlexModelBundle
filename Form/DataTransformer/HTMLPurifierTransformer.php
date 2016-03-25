<?php

namespace FlexModel\FlexModelBundle\Form\DataTransformer;

use HTMLPurifier;
use HTMLPurifier_Config;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * HTMLPurifierTransformer.
 *
 * @author Deborah van der Vegt <deborah@connectholland.nl>
 */
class HTMLPurifierTransformer implements DataTransformerInterface
{
    /**
     * Strip a field of datatype: html from malicious code.
     *
     * @param string $html
     */
    public function transform($html)
    {
        if (class_exists('HTMLPurifier')) {
            $config = HTMLPurifier_Config::createDefault();
            $purifier = new HTMLPurifier($config);
            $cleanHtml = $purifier->purify($html);
        } else {
            $cleanHtml = strip_tags($html, '<a><b><br><i><p><s><u>');
        }

        return $cleanHtml;
    }

    /**
     * Undo stripping of a field of datatype: html from malicious code.
     *
     * @param string $html
     */
    public function reverseTransform($html)
    {
        return $html;
    }
}
