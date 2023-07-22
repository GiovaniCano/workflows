<?php

namespace Tests\Feature\workflows;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

abstract class FormTestAbstract extends TestCase implements FormTestInterface
{
    /**
     * Validate main sections and their content (wysiwyg, images, mini sections and nested sections)
     */
    protected function validateMainSections(): void {
        // Due to the current nature of the 'ValidateSection' Rule used to validate each section, only one error is displayed at a time for each 'main_section'.
        
        $tooLongText = str_repeat('abcdefghijklmnopqrst', 700) . 'a'; // 14,001 (20 * 700 +1)

        /* general */
        $this->assertInvalidSubmit([
            'main_sections' => [
                [
                    'name' => 'name',
                    'id' => '', // required
                    'position' => 0, 
                ],
                [
                    'name' => '', // required
                    'id' => 'stringId',
                    'position' => 1, 
                ],
                [
                    'name' => 'More than 25 characters length', // too long
                    'id' => 'stringId',
                    'position' => 2, 
                ],
                [
                    'name' => 'name',
                    'id' => 'stringId', 
                    'position' => 3, // distinct
                ],
                [
                    'name' => 'name', 
                    'id' => 'stringId',
                    'position' => 3, // distinct
                ],
                [
                    'name' => 'name', 
                    'id' => 'stringId',
                    'position' => null, // required
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 6,
                    'content' => 'string' // content must be an array
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 7,
                    'content' => [ // distinct position in all the content
                        'wysiwygs' => [
                            [
                                'id' => 'stringId', 
                                'position' => 1, // distinct
                            ]
                        ],
                        'sections' => [
                            [
                                'name' => 'name',
                                'id' => 'stringId',
                                'position' => 1, // distinct
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 8,
                    'content' => [ // distinct position in same type of content
                        'wysiwygs' => [
                            [
                                'id' => '', 
                                'position' => 1, // distinct
                                'content' => ''
                            ],
                            [
                                'id' => '', 
                                'position' => 1, // distinct
                                'content' => ''
                            ]
                        ]
                    ]
                ],
            ]
        ], [
            'main_sections.0', // id required
            'main_sections.1', // name required 
            'main_sections.2', // name too long
            'main_sections.3.position', // distinct position
            'main_sections.4.position', // distinct position
            'main_sections.5.position', // position required
            'main_sections.6', // content must be an array
            'main_sections.7', // position distinct in all the content
            'main_sections.8', // distinct position in same type of content
        ]);
        
        /* wysiwygs */
        $this->assertInvalidSubmit([
            'main_sections' => [
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 0,
                    'content' => [
                        'wysiwygs' => [
                            [
                                'id' => '', // required
                                'position' => 0,
                                'content' => ''
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 1,
                    'content' => [
                        'wysiwygs' => [
                            [
                                'id' => 'stringId', 
                                'position' => '', // required
                                'content' => ''
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 2,
                    'content' => [
                        'wysiwygs' => [
                            [
                                'id' => 'stringId', 
                                'position' => 0,
                                'content' => $tooLongText // max length 14,000
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 3,
                    'content' => [
                        'wysiwygs' => 'string', // content.wysiwygs must be an array
                    ]
                ],
            ]
        ], [
            'main_sections.0', // id required
            'main_sections.1', // position required
            'main_sections.2', // max length 14,000
            'main_sections.3', // content.wysiwygs must be an array
        ]);

        /* images */
        $this->assertInvalidSubmit([
            'main_sections' => [
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 0,
                    'content' => [
                        'images' => [
                            ['id' => '', 'position' => 0] // id required
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 1,
                    'content' => [
                        'images' => [
                            ['id' => '', 'position' => null] // position required
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 2,
                    'content' => [
                        'images' => [
                            ['id' => 'stringId', 'position' => 0] // image not uploaded
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 3,
                    'content' => [
                        'images' => 'string', // content.images must be an array
                    ]
                ],
            ]
        ], [
            'main_sections.0', // id required
            'main_sections.1', // position required
            'main_sections.2', // image not uploaded
            'main_sections.3', // content.images must be an array
        ]);

        /* sections */
        $this->assertInvalidSubmit([
            'main_sections' => [
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 0,
                    'content' => [
                        'sections' => [
                            ['name' => '', 'id' => 'stringId', 'position' => 0] // name required
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 1,
                    'content' => [
                        'sections' => [
                            ['name' => 'More than 25 characters length', 'id' => 'stringId', 'position' => 0] // name too long
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 2,
                    'content' => [
                        'sections' => [
                            ['name' => 'name', 'id' => '', 'position' => 0] // id required
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 3,
                    'content' => [
                        'sections' => [
                            ['name' => 'name', 'id' => 'stringId', 'position' => null] // position required
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 4,
                    'content' => [
                        'sections' => [
                            [
                                'name' => 'name', 'id' => 'stringId', 'position' => 0,
                                'content' => 'string' // content must be an array
                            ] 
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 5,
                    'content' => [
                        'sections' => 'string', // content.sections must be an array
                    ]
                ],
            ]
        ], [
            'main_sections.0', // name required
            'main_sections.1', // name too long
            'main_sections.2', // id required
            'main_sections.3', // position required
            'main_sections.4', // content must be an array
            'main_sections.5', // content.sections must be an array
        ]);

        /* minisections */
        $this->assertInvalidSubmit([
            'main_sections' => [
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 0,
                    'content' => [
                        'minisections' => [
                            ['name' => '', 'id' => 'stringId', 'position' => 0] // name required
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 1,
                    'content' => [
                        'minisections' => [
                            ['name' => 'More than 25 characters length', 'id' => 'stringId', 'position' => 0] // name too long
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 2,
                    'content' => [
                        'minisections' => [
                            ['name' => 'name', 'id' => '', 'position' => 0] // id required
                        ],
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 3,
                    'content' => [
                        'minisections' => [
                            ['name' => 'name', 'id' => 'stringId', 'position' => null] // position required
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 4,
                    'content' => [
                        'minisections' => [
                            [
                                'name' => 'name', 'id' => 'stringId', 'position' => 0,
                                'content' => 'string' // content must be an array
                            ] 
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 5,
                    'content' => [
                        'minisections' => 'string', // content.minisections must be an array
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 6,
                    'content' => [
                        'minisections' => [
                            [
                                'name' => 'name', 'id' => 'stringId', 'position' => 0,
                                'content' => [
                                    'sections' => ['no'] // nested sections are not allowed in minisections
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'name' => 'name', 'id' => 'stringId', 'position' => 7,
                    'content' => [
                        'minisections' => [
                            [
                                'name' => 'name', 'id' => 'stringId', 'position' => 0,
                                'content' => [
                                    'minisections' => ['no'] // nested minisections are not allowed in minisections
                                ]
                            ]
                        ]
                    ]
                ],
            ]
        ], [
            'main_sections.0', // name required
            'main_sections.1', // name too long
            'main_sections.2', // id required
            'main_sections.3', // position required
            'main_sections.4', // content.minisections.0.content must be an array
            'main_sections.5', // content.minisections must be an array
            'main_sections.6', // nested sections are not allowed in minisections
            'main_sections.7', // nested minisections are not allowed in minisections
        ]);
    }
}
