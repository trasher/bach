<?php

/**
 * Add all dependencies to the Admin class, this avoid to write too many lines
 * in the configuration files.
 *
 * PHP version 5
 *
 * Copyright (c) 2014, Anaphore
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are
 * met:
 *
 *     (1) Redistributions of source code must retain the above copyright
 *     notice, this list of conditions and the following disclaimer.
 *
 *     (2) Redistributions in binary form must reproduce the above copyright
 *     notice, this list of conditions and the following disclaimer in
 *     the documentation and/or other materials provided with the
 *     distribution.
 *
 *     (3)The name of the author may not be used to
 *    endorse or promote products derived from this software without
 *    specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS OR
 * IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY DIRECT,
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING
 * IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * @category Search
 * @package  Bach
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */

namespace Bach\HomeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Sonata\AdminBundle\Admin\BaseFieldDescription;
use Sonata\AdminBundle\DependencyInjection\Compiler\AddDependencyCallsCompilerPass as CPass;

/**
 * Bach HomeBundle dependency injection extension
 *
 * This is the class that loads and manage bundle configuration
 * See {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 *
 * @category Search
 * @package  Bach
 * @author   Thomas Rabaix <thomas.rabaix@sonata-project.org>
 * @author   Johan Cwiklinski <johan.cwiklinski@anaphore.eu>
 * @license  BSD 3-Clause http://opensource.org/licenses/BSD-3-Clause
 * @link     http://anaphore.eu
 */
class AddDependencyCallsCompilerPass extends CPass
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container Container
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $parameterBag = $container->getParameterBag();
        $groupDefaults = $admins = $classes = array();
        $search_forms = $container->getParameter('search_forms');

        $pool = $container->getDefinition('sonata.admin.pool');

        foreach ($container->findTaggedServiceIds('sonata.admin') as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition = $container->getDefinition($id);

                $arguments = $definition->getArguments();

                if (strlen($arguments[0]) == 0) {
                    $definition->replaceArgument(0, $id);
                }

                if (strlen($arguments[2]) == 0) {
                    $definition->replaceArgument(2, 'SonataAdminBundle:CRUD');
                }

                $this->applyConfigurationFromAttribute($definition, $attributes);
                $this->applyDefaults($container, $id, $attributes);

                $arguments = $definition->getArguments();

                $admins[] = $id;

                if (!isset($classes[$arguments[1]])) {
                    $classes[$arguments[1]] = array();
                }

                $classes[$arguments[1]][] = $id;

                $showInDashboard = (boolean) (isset($attributes['show_in_dashboard']) ?  $parameterBag->resolveValue($attributes['show_in_dashboard']) : true);
                if (!$showInDashboard) {
                    continue;
                }

                $resolvedGroupName = isset($attributes['group']) ? $parameterBag->resolveValue($attributes['group']) : 'default';
                $labelCatalogue = isset($attributes['label_catalogue']) ? $attributes['label_catalogue'] : 'SonataAdminBundle';

                if (!isset($groupDefaults[$resolvedGroupName])) {
                    $groupDefaults[$resolvedGroupName] = array(
                        'label'           => $resolvedGroupName,
                        'label_catalogue' => $labelCatalogue,
                        'roles' => array()
                    );
                }

                $groupDefaults[$resolvedGroupName]['items'][] = $id;

                //Bach: handle facets for other parts
                if ( $id === 'sonata.admin.facets' ) {
                    foreach ( $search_forms as $name=>$search_form ) {
                        $newid = 'sonata.admin.' . $name . 'facets';
                        $admins[] = $newid;
                        $classes[$arguments[1]][] = $newid;
                        $groupDefaults[$resolvedGroupName]['items'][] = $newid;
                    }
                    if ( $container->getParameter('feature.matricules') == true ) {
                        $newid = 'sonata.admin.matriculesfacets';
                        $admins[] = $newid;
                        $classes[$arguments[1]][] = $newid;
                        $groupDefaults[$resolvedGroupName]['items'][] = $newid;
                    }
                }
            }
        }

        $dashboardGroupsSettings = $container->getParameter('sonata.admin.configuration.dashboard_groups');
        if (!empty($dashboardGroupsSettings)) {
            $groups = $dashboardGroupsSettings;

            foreach ($dashboardGroupsSettings as $groupName => $group) {
                $resolvedGroupName = $parameterBag->resolveValue($groupName);
                if (!isset($groupDefaults[$resolvedGroupName])) {
                    $groupDefaults[$resolvedGroupName] = array(
                        'items' => array(),
                        'label' => $resolvedGroupName,
                        'roles' => array()
                    );
                }

                if (empty($group['items'])) {
                    $groups[$resolvedGroupName]['items'] = $groupDefaults[$resolvedGroupName]['items'];
                }

                if (empty($group['label'])) {
                    $groups[$resolvedGroupName]['label'] = $groupDefaults[$resolvedGroupName]['label'];
                }

                if (empty($group['label_catalogue'])) {
                    $groups[$resolvedGroupName]['label_catalogue'] = 'SonataAdminBundle';
                }

                if (!empty($group['item_adds'])) {
                    $groups[$resolvedGroupName]['items'] = array_merge($groups[$resolvedGroupName]['items'], $group['item_adds']);
                }

                if (empty($group['roles'])) {
                    $groups[$resolvedGroupName]['roles'] = $groupDefaults[$resolvedGroupName]['roles'];
                }
            }
        } else {
            $groups = $groupDefaults;
        }

        $pool->addMethodCall('setAdminServiceIds', array($admins));
        $pool->addMethodCall('setAdminGroups', array($groups));
        $pool->addMethodCall('setAdminClasses', array($classes));

        //sets services
        $facets_definition = $container->getDefinition('sonata.admin.facets');
        foreach ( $search_forms as $name=>$search_form ) {
            $newid = 'sonata.admin.' . $name . 'facets';
            $definition = clone $facets_definition;
            $definition->replaceArgument(0, $newid);
            $definition->addArgument($name); //current form
            $definition->addMethodCall(
                'setBaseCodeRoute',
                array($newid)
            );
            $definition->addMethodCall(
                'setBaseRoutePattern',
                array('bach/home/' . $name . 'facets')
            );
            $definition->addMethodCall(
                'setBaseRouteName',
                array('admin_bach_home_' . $name . 'facets')
            );
            $definition->addMethodCall(
                'setClassnameLabel',
                array(_('Facets') . ' (' . $search_form['menu_entry'] . ')')
            );
            $definition->addMethodCall(
                'setLabel',
                array(_('Facets') . ' (' . $search_form['menu_entry'] . ')')
            );
            $container->setDefinition($newid, $definition);
        }

        if ( $container->getParameter('feature.matricules') == true ) {
            $name = 'matricules';
            $newid = 'sonata.admin.' . $name . 'facets';
            $definition = clone $facets_definition;
            $definition->replaceArgument(0, $newid);
            $definition->replaceArgument(
                4,
                $container->getParameter('matricules_corename')
            );
            $definition->addArgument($name); //current form
            $definition->addArgument('Matricules'); //field list class
            $definition->addMethodCall(
                'setBaseCodeRoute',
                array($newid)
            );
            $definition->addMethodCall(
                'setBaseRoutePattern',
                array('bach/home/' . $name . 'facets')
            );
            $definition->addMethodCall(
                'setBaseRouteName',
                array('admin_bach_home_' . $name . 'facets')
            );
            $definition->addMethodCall(
                'setClassnameLabel',
                array(_('Facets') . ' (' . _('Matricules') . ')')
            );
            $definition->addMethodCall(
                'setLabel',
                array(_('Facets') . ' (' . _('Matricules') . ')')
            );
            $container->setDefinition($newid, $definition);
        }

        $routeLoader = $container->getDefinition('sonata.admin.route_loader');
        $routeLoader->replaceArgument(1, $admins);
    }
}
