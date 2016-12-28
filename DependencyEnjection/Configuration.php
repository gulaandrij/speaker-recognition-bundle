<?php
namespace Onekit\SpeakerRecognitionBundle\DependencyInjection;

use \Symfony\Component\Config\Definition\ConfigurationInterface,
    \Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('speaker_recognition');

        $rootNode
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->useAttributeAsKey('name')
            ->prototype('variable')
            ->end()
            ->end();

        return $treeBuilder;
    }

}