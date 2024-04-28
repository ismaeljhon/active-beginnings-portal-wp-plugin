(function($) {
    const $report_form = $('.report-skills-select')
    const $report_checkboxes = $report_form.find('input[type="checkbox"]')
    $report_checkboxes.change(function() {
        let current = $report_checkboxes.filter(':checked').length
        $report_checkboxes.filter(':not(:checked)').prop('disabled', current >= 3)

        if (current.length > 0)
            $report_form.remove('.form-error')
    })

    $report_form.on('submit', function( event ) {
        if ( checkboxes.filter(':checked').length == 0 ) {
            $report_form.prepend('<div class="form-error"><p>Select at least 1 skill</p></div>')
            $([document.documentElement, document.body]).animate({
                scrollTop: $('.heading-note').offset().top
            }, 1000)

            return false
        }

        $report_form.submit()
        return false
    })

    const $skillS_form = $('.skills-form')
    const $skills_checkboxes = $skillS_form.find('input[type="checkbox"]')
    $skills_checkboxes.change(function() {
        let current = skills_checkboxes.filter(':checked').length
        $skills_checkboxes.filter(':not(:checked)').prop('disabled', current >= 3)

        if (current.length > 0)
            $report_form.remove('.form-error')
    })


    $('.expand-description').click(function() {
        $(this).parent('h4').siblings('.skill-description').toggleClass('show')
        return false
    })

    $('input.checkbox-skill-step').click(function() {
        $(this).siblings('.skill-step-desc').toggleClass('show')
    })

    $('.skills-steps .skill.animate__animated:not(:first-child)').hide()

    const $skill_steps = $('.skills-steps')
    const $skills = $('.skill.animate__animated')
    const skill_steps_count = $skills.length 
    let current_step = 1

    $('.skills-form .btn-next').click(function() {
        if ( current_step < skill_steps_count ) {
            $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).addClass('animate__slideOutLeft')
            
            setTimeout(function() {
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).hide().removeClass('animate__slideOutLeft')
                current_step++
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).show().addClass('animate__slideInRight')
                $('.heading-note .page').html(current_step)
            }, 500)
            setTimeout(function() {
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).removeClass('animate__slideInRight')
            }, 1000)
            
            return false
        }
        $('form.skills-form').submit()
    })

    $('.skills-form .btn-back').click(function() {
        if ( current_step > 1 ) {
            $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).addClass('animate__slideOutRight')
            
            setTimeout(function() {
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).hide().removeClass('animate__slideOutRight')
                current_step--
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).show().addClass('animate__slideInLeft')
                console.log(current_step)
                $('.heading-note .page').html(current_step)
            }, 500)

            setTimeout(function() {
                $skill_steps.find(`.skill.animate__animated:nth-child(${current_step})`).removeClass('animate__slideInLeft')
            }, 1000)

            return false
        }

        $('form.back-form').submit()
        return false
    })

    const $student_steps = $('.form-steps')
    const $students = $('.form-step.animate__animated')
    const student_steps_count = $students.length 
    let current_student_step = 1

    const $student_selections = $('.student-selections')
    const $student_skill_select = $student_selections.find('.student-select')
    let student_skill_select_count = $student_skill_select.length
    let current_student_skill_select = 1

    $('.step-2 input[type="checkbox"]').click(function() {
        const student_id = $(this).attr('id')
        $(`.student-select.${student_id}`).toggleClass('student-selected')
        student_skill_select_count = $student_selections.find('.student-selected').length
        
        if ( student_skill_select_count > 1 ) {
            $student_selections.find('.student-selected.animate__animated:not(:first-child)').hide()
        } else {
            $student_selections.find('.student-selected.animate__animated').show()
        }
    })

    $student_steps.find('.form-step.animate__animated:not(:first-child)').hide()

    $('.student-select .btn-next').click(function() {
        if ( current_student_step == 3 ) {
            if ( current_student_skill_select < student_skill_select_count ) {
                $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).addClass('animate__slideOutLeft')
            
                setTimeout(function() {
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).hide().removeClass('animate__slideOutLeft')
                    current_student_skill_select++
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).show().addClass('animate__slideInRight')
                }, 500)
                setTimeout(function() {
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).removeClass('animate__slideInRight')
                }, 1000)
                
                return false
            } else {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).addClass('animate__slideOutLeft')
            
                setTimeout(function() {
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).hide().removeClass('animate__slideOutLeft')
                    current_student_step++
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).show().addClass('animate__slideInRight')
                }, 500)
                setTimeout(function() {
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).removeClass('animate__slideInRight')
                }, 1000)
                
                return false
            }
        }
        if ( current_student_step < student_steps_count ) {  
            $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).addClass('animate__slideOutLeft')
            
            setTimeout(function() {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).hide().removeClass('animate__slideOutLeft')
                current_student_step++
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).show().addClass('animate__slideInRight')
            }, 500)
            setTimeout(function() {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).removeClass('animate__slideInRight')
            }, 1000)

            
            return false
        }
        $('.student-select form').submit()
    })

    $('.student-select .btn-back').click(function() {
        if ( current_student_step == 3 ) {
            if ( current_student_skill_select > 1 ) {
                $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).addClass('animate__slideOutRight')
            
                setTimeout(function() {
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).hide().removeClass('animate__slideOutRight')
                    current_student_skill_select--
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).show().addClass('animate__slideInLeft')
                }, 500)
                setTimeout(function() {
                    $student_selections.find(`.student-selected.animate__animated:nth-child(${current_student_skill_select})`).removeClass('animate__slideInLeft')
                }, 1000)

                
                return false
            } else {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).addClass('animate__slideOutRight')
            
                setTimeout(function() {
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).hide().removeClass('animate__slideOutRight')
                    current_student_step--
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).show().addClass('animate__slideInLeft')
                    console.log(current_student_step)
                }, 500)
    
                setTimeout(function() {
                    $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).removeClass('animate__slideInLeft')
                }, 1000)
    
                return false
            }
        }

        if ( current_student_step > 1 ) {
            $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).addClass('animate__slideOutRight')
            
            setTimeout(function() {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).hide().removeClass('animate__slideOutRight')
                current_student_step--
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).show().addClass('animate__slideInLeft')
                console.log(current_student_step)
                $('.heading-note .page').html(current_student_step)
            }, 500)

            setTimeout(function() {
                $student_steps.find(`.form-step.animate__animated:nth-child(${current_student_step})`).removeClass('animate__slideInLeft')
            }, 1000)

            return false
        }

        $('form.back-form').submit()
        return false
    })

    $('input.student-skill-select').click(function() {
        const skill_id = $(this).attr('id')
        $(`.skill-actions.${skill_id}`).toggleClass('show')
    })

    const $reports = $('.reports-container')
    const $report_screens = $reports.find('.report.animate__animated')
    const report_count = $report_screens.length 
    let current_screen = 1

    $('.report.animate__animated:not(:first-child)').hide()

    $('.report-form .btn-next').click(function() {
        if ( current_screen < report_count ) {
            $reports.find(`.report.animate__animated:nth-child(${current_screen})`).addClass('animate__slideOutLeft')
            
            setTimeout(function() {
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).hide().removeClass('animate__slideOutLeft')
                current_screen++
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).show().addClass('animate__slideInRight')
                $('.heading-note .page').html(current_screen)
            }, 500)
            setTimeout(function() {
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).removeClass('animate__slideInRight')
            }, 1000)

            if (current_screen + 1 == report_count )
                $('.report-form .btn-next').text('Print')
            
            return false
        }
        $('form.report-form').submit()
    })

    $('.report-form .btn-back').click(function() {
        if ( current_screen > 1 ) {
            $reports.find(`.report.animate__animated:nth-child(${current_screen})`).addClass('animate__slideOutRight')
            
            setTimeout(function() {
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).hide().removeClass('animate__slideOutRight')
                current_screen--
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).show().addClass('animate__slideInLeft')
                console.log(current_screen)
                $('.heading-note .page').html(current_screen)
            }, 500)

            setTimeout(function() {
                $reports.find(`.report.animate__animated:nth-child(${current_screen})`).removeClass('animate__slideInLeft')
            }, 1000)

            return false
        }

        $('form.back-form').submit()
        return false
    })

    $('.btn-back.back-form').click(function() {
        $('form.back-form').submit()
        return false
    })
})(jQuery);